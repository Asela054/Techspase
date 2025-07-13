<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
use DatePeriod;
use DateInterval;
use DateTime;
use PDF;

class EmployeeAttedanceReportContrller extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('attendance-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();
        return view('Report.employee_attendance_report', compact('companies'));
    }

    public function generatereport(Request $request)
    {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $lastEmpId = $request->get('last_emp_id', 0); // Start from last loaded employee

        $limit = 100; // Load one employee at a time

        // Step 1: Fetch Employee Data (unchanged)
        $employees = DB::select("
            SELECT emp.id, emp.emp_id, emp.emp_etfno, emp.emp_fullname, emp.emp_gender, 
                dept.name AS departmentname, job.title AS jobtitlename, emp.emp_shift, 
                COALESCE(esd_shift.shift_name, st.shift_name) AS shiftname
            FROM employees emp
            LEFT JOIN departments dept ON emp.emp_department = dept.id
            LEFT JOIN job_titles job ON emp.emp_job_code = job.id
            LEFT JOIN shift_types st ON emp.emp_shift = st.id
            LEFT JOIN employeeshiftdetails esd 
                ON esd.emp_id = emp.id
            LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
            WHERE emp.deleted = 0
            AND emp.is_resigned = 0
            AND emp.status = 1 
            AND emp.emp_department = ? 
            AND emp.id > ?
            ORDER BY emp.id ASC 
            LIMIT ?",
            [$department, $lastEmpId, $limit]
        );

        if (empty($employees)) {
            return response()->json([
                'data' => [],
                'lastEmpId' => null
            ]);
        }

        // Step 2: Generate date range in PHP (alternative to recursive CTE)
        $startDate = new DateTime($from_date);
        $endDate = new DateTime($to_date);
        $dateRange = [];
        
        while ($startDate <= $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }

        $employeeData = [];
        foreach ($employees as $employee) {
            if(!empty($employee->emp_shift)){
                // Initialize attendance records array
                $attendanceRecords = [];
                
                // Process each date in the range
                foreach ($dateRange as $date) {
                    $record = DB::select("
                        SELECT 
                            DATE_FORMAT(?, '%Y-%m-%d') AS in_date,
                            DATE_FORMAT(?, '%Y-%m-%d') AS out_date,
                            COALESCE(h.holiday_name, 
                                CASE WHEN WEEKDAY(?) IN (5,6) THEN DAYNAME(?) 
                                ELSE 'Weekday' END) AS day_type,
                            COALESCE(esd_shift.shift_name, st.shift_name) AS shift,
                            DATE_FORMAT(MIN(att.timestamp), '%h:%i %p') AS in_time, 
                            DATE_FORMAT(MAX(att.timestamp), '%h:%i %p') AS out_time,
                            ROUND(COALESCE(la.minites_count, 0), 2) AS late_min, 
                            COALESCE(leave_data.leavename, '') AS leave_type, 
                            ROUND(COALESCE(leave_data.no_of_days, 0), 2) AS leave_days,
                            ROUND(COALESCE(ot.hours, 0) + COALESCE(ot.holiday_normal_hours, 0) + 
                                COALESCE(ot.poya_extended_normal_ot_hrs, 0), 2) AS ot_hours,
                            ROUND(COALESCE(ot.double_hours, 0) + COALESCE(ot.holiday_double_hours, 0) + 
                                COALESCE(ot.sunday_double_ot_hrs, 0), 2) AS double_ot,
                            ROUND(COALESCE(ot.triple_hours, 0), 2) AS triple_ot
                        FROM (SELECT ? AS date) dr
                        LEFT JOIN attendances att ON att.emp_id = ? AND att.date = ?
                        LEFT JOIN shift_types st ON st.id = ?
                        LEFT JOIN employeeshiftdetails esd 
                            ON esd.emp_id = ? AND ? BETWEEN esd.date_from AND esd.date_to
                        LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
                        LEFT JOIN employee_late_attendance_minites la 
                            ON la.emp_id = ? AND la.attendance_date = ?
                        LEFT JOIN (
                            SELECT ot.emp_id, ot.date, ot.hours, ot.double_hours, ot.triple_hours, 
                                ot.holiday_normal_hours, ot.holiday_double_hours, 
                                ot.sunday_double_ot_hrs, ot.poya_extended_normal_ot_hrs
                            FROM ot_approved ot
                        ) ot ON ot.emp_id = ? AND ot.date = ?
                        LEFT JOIN (
                            SELECT l.emp_id, lt.leave_type AS leavename, l.no_of_days, l.leave_from, l.leave_to
                            FROM leaves l
                            LEFT JOIN leave_types lt ON l.leave_type = lt.id
                            WHERE l.status = 'Approved'
                        ) leave_data ON leave_data.emp_id = ? AND ? BETWEEN leave_data.leave_from AND leave_data.leave_to
                        LEFT JOIN holidays h ON h.date = ?
                        GROUP BY ?
                    ", [
                        $date, $date, $date, $date, // For date formatting and weekday check
                        $date, // For the dr alias
                        $employee->emp_id, $date, // For attendance join
                        $employee->emp_shift, // For shift_types join
                        $employee->emp_id, $date, // For employeeshiftdetails join
                        $employee->emp_id, $date, // For late attendance join
                        $employee->emp_id, $date, // For OT join
                        $employee->emp_id, $date, // For leave join
                        $date, // For holiday join
                        $date  // For GROUP BY
                    ]);

                    // Add the record (we take the first result since we're querying one date at a time)
                    $attendanceRecords[] = $record[0] ?? [
                        'in_date' => $date,
                        'out_date' => $date,
                        'day_type' => '',
                        'shift' => '',
                        'in_time' => '',
                        'out_time' => '',
                        'late_min' => 0,
                        'leave_type' => '',
                        'leave_days' => 0,
                        'ot_hours' => 0,
                        'double_ot' => 0,
                        'triple_ot' => 0
                    ];
                }

                // Store Employee Data
                $employeeData[] = [
                    'id' => $employee->id,
                    'emp_id' => $employee->emp_id,
                    'emp_etfno' => $employee->emp_etfno,
                    'emp_fullname' => $employee->emp_fullname,
                    'jobtitlename' => $employee->jobtitlename,
                    'departmentname' => $employee->departmentname,
                    'emp_gender' => $employee->emp_gender,
                    'shiftname' => $employee->shiftname,
                    'attendance' => $attendanceRecords
                ];

                // Update last loaded employee ID
                $lastEmpId = $employee->id;
            }
        }

        $pdfData[] = [
            'data' => $employeeData,
            'lastEmpId' => $lastEmpId
        ];

        echo json_encode($pdfData);
    }

    // PHP date range generator
    // private function createDateRangeArray($start, $end)
    // {
    //     $range = [];
    //     $startDate = Carbon::parse($start);
    //     $endDate = Carbon::parse($end);

    //     for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
    //         $range[] = $date->format('Y-m-d');
    //     }

    //     return $range;
    // }


    // public function generatereport(Request $request)
    // {
    //     $department = $request->get('department');
    //     $from_date = $request->get('from_date');
    //     $to_date = $request->get('to_date');
    //     $lastEmpId = $request->get('last_emp_id', 0); // Start from last loaded employee

    //     $limit = 100; // Load one employee at a time

    //     // Step 1: Fetch Employee Data
    //     $employees = DB::select("
    //         SELECT emp.id, emp.emp_id, emp.emp_etfno, emp.emp_fullname, emp.emp_gender, 
    //             dept.name AS departmentname, job.title AS jobtitlename, emp.emp_shift, 
    //             COALESCE(esd_shift.shift_name, st.shift_name) AS shiftname
    //         FROM employees emp
    //         LEFT JOIN departments dept ON emp.emp_department = dept.id
    //         LEFT JOIN job_titles job ON emp.emp_job_code = job.id
    //         LEFT JOIN shift_types st ON emp.emp_shift = st.id
    //         LEFT JOIN employeeshiftdetails esd 
    //             ON esd.emp_id = emp.id
    //         LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
    //         WHERE emp.deleted = 0 
    //         AND emp.emp_department = ? 
    //         AND emp.id > ?
    //         ORDER BY emp.id ASC 
    //         LIMIT ?",
    //         [$department, $lastEmpId, $limit]
    //     );

    //     if (empty($employees)) {
    //         return response()->json([
    //             'data' => [],
    //             'lastEmpId' => null
    //         ]);
    //     }

    //     // Step 2: Fetch Attendance for Each Employee
    //     $employeeData = [];
    //     foreach ($employees as $employee) {
    //         if(!empty($employee->emp_shift)){
    //             $attendanceRecords = DB::select("
    //                 WITH RECURSIVE date_range AS (
    //                     SELECT DATE(?) as date
    //                     UNION ALL
    //                     SELECT DATE_ADD(date, INTERVAL 1 DAY)
    //                     FROM date_range
    //                     WHERE date < DATE(?)
    //                 )
    //                 SELECT 
    //                     DATE_FORMAT(dr.date, '%Y-%m-%d') AS in_date,
    //                     DATE_FORMAT(dr.date, '%Y-%m-%d') AS out_date,
    //                     COALESCE(h.holiday_name, 
    //                         CASE WHEN WEEKDAY(dr.date) IN (5,6) THEN DAYNAME(dr.date) 
    //                         ELSE 'Weekday' END) AS day_type,
    //                     COALESCE(esd_shift.shift_name, st.shift_name) AS shift,
    //                     DATE_FORMAT(MIN(att.timestamp), '%h:%i %p') AS in_time, 
    //                     DATE_FORMAT(MAX(att.timestamp), '%h:%i %p') AS out_time,
    //                     ROUND(COALESCE(la.minites_count, 0), 2) AS late_min, 
    //                     COALESCE(leave_data.leavename, '') AS leave_type, 
    //                     ROUND(COALESCE(leave_data.no_of_days, 0), 2) AS leave_days,
    //                     ROUND(COALESCE(ot.hours, 0) + COALESCE(ot.holiday_normal_hours, 0) + 
    //                         COALESCE(ot.poya_extended_normal_ot_hrs, 0), 2) AS ot_hours,
    //                     ROUND(COALESCE(ot.double_hours, 0) + COALESCE(ot.holiday_double_hours, 0) + 
    //                         COALESCE(ot.sunday_double_ot_hrs, 0), 2) AS double_ot,
    //                     ROUND(COALESCE(ot.triple_hours, 0), 2) AS triple_ot
    //                 FROM date_range dr
    //                 LEFT JOIN attendances att ON att.emp_id = ? AND att.date = dr.date
    //                 LEFT JOIN shift_types st ON st.id = ?
    //                 LEFT JOIN employeeshiftdetails esd 
    //                     ON esd.emp_id = ? AND dr.date BETWEEN esd.date_from AND esd.date_to
    //                 LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
    //                 LEFT JOIN employee_late_attendance_minites la 
    //                     ON la.emp_id = ? AND la.attendance_date = dr.date
    //                 LEFT JOIN (
    //                     SELECT ot.emp_id, ot.date, ot.hours, ot.double_hours, ot.triple_hours, 
    //                         ot.holiday_normal_hours, ot.holiday_double_hours, 
    //                         ot.sunday_double_ot_hrs, ot.poya_extended_normal_ot_hrs
    //                     FROM ot_approved ot
    //                 ) ot ON ot.emp_id = ? AND ot.date = dr.date
    //                 LEFT JOIN (
    //                     SELECT l.emp_id, lt.leave_type AS leavename, l.no_of_days, l.leave_from, l.leave_to
    //                     FROM leaves l
    //                     LEFT JOIN leave_types lt ON l.leave_type = lt.id
    //                     WHERE l.status = 'Approved'
    //                 ) leave_data ON leave_data.emp_id = ? AND dr.date BETWEEN leave_data.leave_from AND leave_data.leave_to
    //                 LEFT JOIN holidays h ON h.date = dr.date
    //                 GROUP BY dr.date
    //                 ORDER BY dr.date ASC
    //             ", [
    //                 $from_date, $to_date,
    //                 $employee->emp_id,
    //                 $employee->emp_shift,
    //                 $employee->emp_id,
    //                 $employee->emp_id,
    //                 $employee->emp_id,
    //                 $employee->emp_id,
    //                 $employee->emp_id
    //             ]);


    //             // Store Employee Data
    //             $employeeData[] = [
    //                 'id' => $employee->id,
    //                 'emp_id' => $employee->emp_id,
    //                 'emp_etfno' => $employee->emp_etfno,
    //                 'emp_fullname' => $employee->emp_fullname,
    //                 'jobtitlename' => $employee->jobtitlename,
    //                 'departmentname' => $employee->departmentname,
    //                 'emp_gender' => $employee->emp_gender,
    //                 'shiftname' => $employee->shiftname,
    //                 'attendance' => $attendanceRecords // Attach all attendance records
    //             ];

    //             // Update last loaded employee ID
    //             $lastEmpId = $employee->id;
    //         }
    //     }

    //     $pdfData[] = [
    //         'data' => $employeeData,
    //         'lastEmpId' => $lastEmpId
    //     ];

    //     echo json_encode($pdfData);
    // }


    // public function generatereport(Request $request)
    // {
    //     // $department = $request->get('department');
    //     // $from_date = $request->get('from_date');
    //     // $to_date = $request->get('to_date');

    //     // $page = $request->get('page', 1);

    //     // $lastEmpId = $request->get('lastEmpId', 0);
    //     // $limit = 1;
    //     // $limit = 30; // Load 20 employees per request
    //     // $offset = ($page - 1) * $limit;
    
    //     // DB::enableQueryLog();
    //     // $employees = DB::select(
    //     //     "SELECT emp.id, 
    //     //             emp.emp_id, 
    //     //             emp.emp_etfno, 
    //     //             emp.emp_fullname, 
    //     //             emp.emp_gender, 
    //     //             dept.name AS departmentname, 
    //     //             job.title AS jobtitlename, 
    //     //             COALESCE(esd_shift.shift_name, st.shift_name) AS shiftname,
    //     //             att.in_time, 
    //     //             att.out_time, 
    //     //             att.max_date,
    //     //             COALESCE(la.minites_count, 0) AS late_min, 
    //     //             COALESCE(leave_data.leavename, '') AS leave_type, 
    //     //             COALESCE(leave_data.no_of_days, '') AS leave_days, 
    //     //             COALESCE(ot.hours, 0) + COALESCE(ot.holiday_normal_hours, 0) + COALESCE(ot.poya_extended_normal_ot_hrs, 0) AS ot_hours,
    //     //             COALESCE(ot.double_hours, 0) + COALESCE(ot.holiday_double_hours, 0) + COALESCE(ot.sunday_double_ot_hrs, 0) AS double_ot,
    //     //             COALESCE(ot.triple_hours, 0) AS triple_ot,
    //     //             COALESCE(h.holiday_name, CASE WHEN WEEKDAY(att.max_date) IN (5,6) THEN DAYNAME(att.max_date) ELSE 'Weekday' END) AS day_type
    //     //      FROM employees emp
    //     //      LEFT JOIN departments dept ON emp.emp_department = dept.id
    //     //      LEFT JOIN job_titles job ON emp.emp_job_code = job.id
    //     //      LEFT JOIN shift_types st ON emp.emp_shift = st.id
    //     //      LEFT JOIN (
    //     //          SELECT emp_id, date AS max_date, MIN(timestamp) AS in_time, MAX(timestamp) AS out_time
    //     //          FROM attendances 
    //     //          WHERE date BETWEEN ? AND ? 
    //     //          GROUP BY emp_id, date
    //     //      ) att ON att.emp_id = emp.emp_id
    //     //      LEFT JOIN employeeshiftdetails esd 
    //     //          ON esd.emp_id = emp.id AND att.max_date BETWEEN esd.date_from AND esd.date_to
    //     //      LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
    //     //      LEFT JOIN employee_late_attendance_minites la 
    //     //          ON la.emp_id = emp.emp_id AND la.attendance_date = att.max_date
    //     //      LEFT JOIN (
    //     //          SELECT ot.emp_id, ot.date, ot.hours, ot.double_hours, ot.triple_hours, 
    //     //                 ot.holiday_normal_hours, ot.holiday_double_hours, 
    //     //                 ot.sunday_double_ot_hrs, ot.poya_extended_normal_ot_hrs
    //     //          FROM ot_approved ot
    //     //      ) ot ON ot.emp_id = emp.emp_id AND ot.date = att.max_date
    //     //      LEFT JOIN (
    //     //          SELECT l.emp_id, lt.leave_type AS leavename, l.no_of_days, l.leave_from, l.leave_to
    //     //          FROM leaves l
    //     //          LEFT JOIN leave_types lt ON l.leave_type = lt.id
    //     //          WHERE l.status = 'Approved'
    //     //      ) leave_data ON leave_data.emp_id = emp.id AND att.max_date BETWEEN leave_data.leave_from AND leave_data.leave_to
    //     //      LEFT JOIN holidays h ON h.date = att.max_date
    //     //      WHERE emp.deleted = 0 AND emp.emp_department = ?
    //     //      GROUP BY emp.emp_id, att.max_date
    //     //      ORDER BY emp.emp_id
    //     //       LIMIT ? OFFSET ?",
    //     //     [$from_date, $to_date, $department, $limit, $recordcount]
    //     // );
        
    //     // // Get the query log
    //     // $queryLog = DB::getQueryLog();
    //     // $query = end($queryLog); // Get the last executed query

    //     // // Replace bindings in the query
    //     // $sql = vsprintf(str_replace('?', "'%s'", $query['query']), $query['bindings']);

    //     // return $sql;

    //     $employeeData = [];
    //     foreach ($employees as $employee) {
    //         // Check if the employee is already in the array
    //         if (!isset($employeeData[$employee->id])) {
    //             $employeeData[$employee->id] = [
    //                 'id' => $employee->id,
    //                 'emp_id' => $employee->emp_id,
    //                 'emp_etfno' => $employee->emp_etfno,
    //                 'emp_fullname' => $employee->emp_fullname,
    //                 'jobtitlename' => $employee->jobtitlename,
    //                 'departmentname' => $employee->departmentname,
    //                 'emp_gender' => $employee->emp_gender,
    //                 'shiftname' => $employee->shiftname,
    //                 'attendance' => [] // Initialize attendance array
    //             ];
    //         }

    //         // Append attendance data
    //         $employeeData[$employee->id]['attendance'][] = [
    //             'in_date' => $employee->max_date,
    //             'out_date' => isset($employee->max_date) ? \Carbon\Carbon::parse($employee->max_date)->format('Y-m-d') : ' ',
    //             'day_type' => $employee->day_type,
    //             'shift' => $employee->shiftname,
    //             'in_time' => $employee->in_time,
    //             'out_time' => $employee->out_time,
    //             'late_min' => $employee->late_min,
    //             'ot_hours' => $employee->ot_hours,
    //             'double_ot' => $employee->double_ot,
    //             'triple_ot' => $employee->triple_ot,
    //             'leave_type' => $employee->leave_type,
    //             'leave_days' => $employee->leave_days,
    //         ];
    //     }

    //     // Convert the associative array to a standard indexed array
    //     $pdfData = array_values($employeeData);
    //     echo json_encode($pdfData);






    //     // $pdfData = [];
    //     // $empautoID=0;
    //     // foreach ($employees as $employee) {
    //     //     if($empautoID!=$employee->id){
    //     //         $empautoID = $employee->id;
    //     //         $employelist[] = [
    //     //             'id' => $employee->id,
    //     //             'emp_id' => $employee->emp_id,
    //     //             'emp_etfno' => $employee->emp_etfno,
    //     //             'jobtitlename' => $employee->jobtitlename,
    //     //             'departmentname' => $employee->departmentname,
    //     //             'emp_gender' => $employee->emp_gender,
    //     //         ];
    //     //     }

    //     //     $attendanceData[] = [
    //     //         'in_date' => $employee->max_date,
    //     //         'out_date' => isset($employee->max_date) ? \Carbon\Carbon::parse($employee->max_date)->format('Y-m-d') : ' ',
    //     //         'day_type' => $employee->day_type,
    //     //         'shift' => $employee->shiftname,
    //     //         'in_time' => $employee->in_time,
    //     //         'out_time' => $employee->out_time,
    //     //         'late_min' => $employee->late_min,
    //     //         'ot_hours' => $employee->ot_hours,
    //     //         'double_ot' => $employee->double_ot,
    //     //         'triple_ot' => $employee->triple_ot,
    //     //         'leave_type' => $employee->leave_type,
    //     //         'leave_days' => $employee->leave_days,
    //     //     ];
            
    //     //     $pdfData[] = [
    //     //         'employee' => json_encode($employelist),
    //     //         'attendance' => json_encode($attendanceData),
    //     //     ];
    //     // }
    //     // // return response()->json($pdfData);
    //     // echo json_encode($pdfData);
    //     // ini_set("memory_limit", "999M");
    //     // ini_set("max_execution_time", "999");
    
    //     // $pdf = Pdf::loadView('Report.attendaceemployeereportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
    //     // return $pdf->download('Employee Attedance Report.pdf');
    // }
}


// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Auth;
// use DateInterval;
// use DatePeriod;
// use DateTime;
// use Illuminate\Support\Facades\DB;
// use PDF;

// class EmployeeAttedanceReportContrller extends Controller
// {
//     public function index()
//     {
//         $permission = Auth::user()->can('attendance-report');
//         if (!$permission) {
//             abort(403);
//         }
//         $companies = DB::table('companies')->select('*')->get();
//         return view('Report.employee_attendance_report', compact('companies'));
//     }


//     public function generatereport(Request $request) {
//         $department = $request->get('department');
//         $from_date = $request->get('from_date');
//         $to_date = $request->get('to_date');
//         $from_range = $request->get('from_range', 0);
//         $to_range = $request->get('to_range', 20);
    
//         $period = new DatePeriod(
//             new DateTime($from_date),
//             new DateInterval('P1D'), 
//             new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
//         );
    
//         $from_range = max(0, (int) $from_range);
//         $to_range = max($from_range, (int) $to_range);
//         $limit = $to_range - $from_range; 

//         $employees = DB::table('employees')
//             ->select(
//                 'employees.id', 
//                 'employees.emp_id', 
//                 'employees.emp_fullname', 
//                 'employees.emp_gender',
//                 'departments.name AS departmentname',
//                 'job_titles.title AS jobtitlename',
//                 'shift_types.shift_name AS shiftname'
//             )
//             ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
//             ->leftJoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
//             ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
//             ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
//             ->where('employees.deleted', 0)
//             ->where('employees.emp_department', $department)
//             ->whereBetween('attendances.date', [$from_date, $to_date])
//             ->groupBy('employees.id')
//             ->orderBy('employees.emp_id')
//             ->offset($from_range) 
//             ->limit($limit)
//             ->get();

//         $pdfData = [];
    
//         foreach ($employees as $employee) {
//             $attendanceData = [];
    
//             foreach ($period as $date) {
//                 $currentDate = $date->format('Y-m-d');
//                 $dayType = in_array($date->format('l'), ['Saturday', 'Sunday']) ? $date->format('l') : 'Weekday';
    
//                 $attendance = DB::table('attendances')
//                     ->where('emp_id', $employee->emp_id)
//                     ->whereDate('date', $currentDate)
//                     ->selectRaw('MIN(timestamp) as in_time, MAX(timestamp) as out_time, MAX(date) as max_date')
//                     ->first();
    
//                 $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
//                 $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';
    
//                 $shiftCheck = DB::table('employeeshiftdetails')
//                     ->where('emp_id', $employee->id)
//                     ->whereDate('date_from', '<=', $currentDate)
//                     ->whereDate('date_to', '>=', $currentDate)
//                     ->exists();
                
//                 $shift_name = $shiftCheck ? 'Night Shift' : $employee->shiftname;
    
//                 $lateMinutes = DB::table('employee_late_attendance_minites')
//                     ->where('emp_id', $employee->emp_id)
//                     ->whereDate('attendance_date', $currentDate)
//                     ->value('minites_count') ?? 0;
    
//                 $otApproved = DB::table('ot_approved')
//                     ->where('emp_id', $employee->emp_id)
//                     ->whereDate('date', $currentDate)
//                     ->select('hours', 'double_hours', 'triple_hours', 'holiday_normal_hours', 'holiday_double_hours', 'sunday_double_ot_hrs', 'poya_extended_normal_ot_hrs')
//                     ->first();

    
//                 $leave = DB::table('leaves')
//                     ->select('leave_types.leave_type AS leavename', 'leaves.no_of_days')
//                     ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
//                     ->where('emp_id', $employee->id)
//                     ->whereDate('leave_from', '<=', $currentDate)
//                     ->whereDate('leave_to', '>=', $currentDate)
//                     ->where('status', 'Approved')
//                     ->first();
    
//                 $holiday = DB::table('holidays')
//                     ->where('date', $currentDate)
//                     ->select('holiday_name','holiday_type')
//                     ->first();

//                     // normal ot 
//                     $othur = $otApproved->hours ?? '0.00';
//                     $holidayotHours = $otApproved->holiday_normal_hours ?? '0.00';
//                     $poyaExtendedHours = $otApproved->poya_extended_normal_ot_hrs ?? '0.00';
                    
//                     $doubleot = $otApproved->double_hours ?? '0.00';
//                     $sundaydouble = $otApproved->sunday_double_ot_hrs ?? '0.00';
//                     $holidaydoubleOT = $otApproved->holiday_double_hours ?? '0.00';

//                     $tripleOT = $otApproved->triple_hours ?? '0.00';

//                   // Convert all values to float
//                     $othur = (float) $othur;
//                     $holidayotHours = (float) $holidayotHours;
//                     $poyaExtendedHours = (float) $poyaExtendedHours;

//                     $doubleot = (float) $doubleot;
//                     $sundaydouble = (float) $sundaydouble;
//                     $holidaydoubleOT = (float) $holidaydoubleOT;
//                     $tripleOT = (float) $tripleOT;

//                     // Perform calculations
//                     $otHours_total = $othur + $holidayotHours + $poyaExtendedHours;
//                     $doubleOT_total = $doubleot + $holidaydoubleOT + $sundaydouble;

//                     // Round the values to 2 decimal places for accuracy
//                     $otHours = round($otHours_total, 2);
//                     $doubleOT = round($doubleOT_total, 2);
//                     $tripleOT = round($tripleOT, 2);

               

//                 $attendanceData[] = [
//                     'in_date' => $currentDate,
//                     'out_date' => isset($attendance->max_date) ? \Carbon\Carbon::parse($attendance->max_date)->format('Y-m-d') : ' ',
//                     'day_type' => $holiday->holiday_name ?? $dayType,
//                     'shift' => $shift_name,
//                     'in_time' => $inTime,
//                     'out_time' => $outTime,
//                     'late_min' => $lateMinutes,
//                     'ot_hours' => $otHours,
//                     'double_ot' => $doubleOT,
//                     'triple_ot' => $tripleOT,
//                     'leave_type' => $leave->leavename ?? '',
//                     'leave_days' => $leave->no_of_days ?? '',
//                 ];
//             }
            
//             $pdfData[] = [
//                 'employee' => $employee,
//                 'attendance' => $attendanceData,
//             ];
//         }

//         ini_set("memory_limit", "999M");
// 		ini_set("max_execution_time", "999");

//         $pdf = Pdf::loadView('Report.attendaceemployeereportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
//         return $pdf->download('Employee Attedance Report.pdf');
//     }
    
// }
