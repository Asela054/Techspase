<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
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

    // public function generatereport(Request $request){
    //     $department = $request->get('department');
    //     $from_date = $request->get('from_date');
    //     $to_date = $request->get('to_date');

      
    //     $period = new DatePeriod(
    //         new DateTime($from_date),
    //         new DateInterval('P1D'), 
    //         new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
    //     );


    //     $query = DB::table('employees')
    //     ->select('employees.*','departments.name AS departmentname','job_titles.title AS jobtitlename','shift_types.shift_name AS shiftname')
    //     ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
    //     ->leftjoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
    //     ->leftjoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
    //     ->where('employees.deleted', '=',0)
    //     ->where('employees.emp_department', '=', $department)
    //     ->orderBy('employees.id')
    //     ->get();

        
    //     // dd($query);

    //     foreach ($query as $row) {
    //         $employee_id = $row->id;
    //         $empName = $row->emp_fullname;
    //         $empId = $row->emp_id;
    //         $gender = $row->emp_gender;
    //         $empdepartment = $row->departmentname;
    //         $jobtitle = $row->jobtitlename;
    //         $shift_name = $row->shiftname;

    //         $tablebody ='';

    //         foreach ($period as $date) {
    //             $currentDate = $date->format('Y-m-d');

    //             $dayType = $date->format('l');
    //             if ($dayType == 'Saturday' || $dayType == 'Sunday') {
    //                 $dayType = $dayType; 
    //             } else {
    //                 $dayType = 'Weekday';
    //             }

    //             $attendance = DB::table('attendances')
    //             ->where('emp_id', $empId)
    //             ->whereDate('date', $currentDate)
    //             ->selectRaw('MIN(timestamp) as in_time, MAX(timestamp) as out_time, MAX(date) as max_date')
    //             ->first();

    //             $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
    //             $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';

                
    //             if(!empty($attendance)){

    //                 // Check that employee is assign to night shift
    //                 $shiftCheck = DB::table('employeeshiftdetails')
    //                 ->where('emp_id', $empId)
    //                 ->whereDate('date_from', '<=', $currentDate)
    //                 ->whereDate('date_to', '>=', $currentDate)
    //                 ->exists();
    //                 $shift_name = $shiftCheck ? 'Night Shift' : $shift_name;



    //                 $tablebody .='<tr> 
    //                 <td>'. $currentDate.'</td>
    //                 <td>' . ($attendance->max_date ?? ' ') . '</td>
    //                 <td>' . $dayType . '</td>
    //                 <td>' . $shift_name.'</td>
    //                 <td>' . $inTime. '</td>
    //                 <td>' . $outTime.'</td>';


    //                 $lateminitesattendance = DB::table('employee_late_attendance_minites')
    //                 ->where('emp_id', $empId)
    //                 ->whereDate('attendance_date', $currentDate)
    //                 ->select('minites_count as minitescount')
    //                 ->first();

    //                 if(!empty($lateminitesattendance)){
    //                     $tablebody .='<td>'. $lateminitesattendance->minitescount.'</td>';
    //                 }else{
    //                     $tablebody .='<td> 0 </td>';
    //                 }

    //                 $otapproved = DB::table('ot_approved')
    //                 ->where('emp_id', $empId)
    //                 ->whereDate('date', $currentDate)
    //                 ->select('ot_approved.*')
    //                 ->first();

    //                 if(!empty($otapproved)){
    //                     $tablebody .='<td>'. $otapproved->hours.'</td>
    //                                   <td>' . $otapproved->double_hours . '</td>
    //                                   <td>' . $otapproved->triple_hours . '</td>
    //                                   <td>  </td>
    //                                   <td>  </td>';     

    //                 }else{
    //                     $tablebody .='<td> 0:00 </td>
    //                     <td> 0:00 </td>
    //                     <td> 0:00 </td>
    //                     <td>  </td>
    //                     <td>  </td>';
    //                 }


    //             }else{

    //                 $leave = DB::table('leaves')
    //                 ->select('leaves.*','leave_types.leave_type AS leavename')
    //                 ->leftjoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
    //                 ->where('emp_id', $employee_id)
    //                 ->whereDate('leave_from', '<=', $currentDate)
    //                 ->whereDate('leave_to', '>=', $currentDate)
    //                 ->where('status', 'Approved')
    //                 ->first();
    //                 if(!empty($leave)){

    //                     $tablebody .='<tr> 
    //                     <td>'. $currentDate.'</td>
    //                     <td>  </td>
    //                     <td>' . $dayType . '</td>
    //                     <td> '.$shift_name.' </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>' . $leave->leavename . '</td>
    //                     <td> '. $leave->no_of_days.' </td>';

    //                 }else{

    //                 $holiday = DB::table('holidays')
    //                 ->select('holidays.*')
    //                 ->where('date', '=', $currentDate)
    //                 ->first();
    //                 if(!empty($holiday)){
                        
    //                     $tablebody .='<tr> 
    //                     <td>'. $currentDate.'</td>
    //                     <td>  </td>
    //                     <td>' .$holiday->holiday_name.'</td>
    //                     <td> '.$shift_name.'</td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>';

    //                 }else{
    //                     $tablebody .='<tr> 
    //                     <td>'. $currentDate.'</td>
    //                     <td>  </td>
    //                     <td>' . $dayType . '</td>
    //                     <td> '.$shift_name.'</td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>
    //                     <td>  </td>';
    //                 }
    //                 }
    //             }
    //         }

    //         $html= '<!DOCTYPE html>
    //                         <html lang="en">
    //                         <head>
    //                             <meta charset="UTF-8">
    //                             <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //                             <title>Employee Attendance Report</title>
    //                             <style>
    //                                 body {
    //                                     font-family: Arial, sans-serif;
    //                                     font-size: 11px;
    //                                 }
    //                                 .report-table {
    //                                     width: 100%;
    //                                     border-collapse: collapse;
    //                                     margin-top:2px;
    //                                 }
    //                                 .report-table, .report-table th, .report-table td {
    //                                     border: 1px solid black;
    //                                 }
    //                                 .report-table th{
    //                                     padding: 1px;
    //                                     text-align: left;
    //                                 }
    //                                 .header-table {
    //                                     width: 100%;
    //                                     border: 1px solid black;
    //                                     margin-bottom:3px;
    //                                 }
    //                                 .header-table td {
    //                                     padding: 1px;
    //                                 }
    //                                 .bold {
    //                                     font-weight: bold;
    //                                 }
    //                                 .highlight {
    //                                     background-color: yellow;
    //                                 }
    //                             </style>
    //                         </head>
    //                         <body>
    //                             <table class="table">
    //                                 <tr>
    //                                     <td class="bold">Emp No</td>
    //                                     <td>'. $empId.'</td>
    //                                     <td class="bold">Department</td>
    //                                     <td>'. $empdepartment.'</td>
    //                                 </tr>
    //                                 <tr>
    //                                     <td class="bold">Name</td>
    //                                     <td>'. $empName.'</td>
    //                                     <td class="bold">Gender</td>
    //                                     <td>'. $gender.'</td>
    //                                 </tr>
    //                                 <tr>
    //                                     <td class="bold">Designation</td>
    //                                     <td>'. $jobtitle.'</td>
    //                                     <td class="bold">Thumb ID</td>
    //                                     <td>'. $empId.'</td>
    //                                 </tr>
    //                                 <tr>
    //                                     <td class="bold">Grade</td>
    //                                     <td>N/A</td>
    //                                 </tr>
    //                             </table>

    //                             <table class="report-table">
    //                                 <tr>
    //                                     <th>In Date</th>
    //                                     <th>Out Date</th>
    //                                     <th>Day Type</th>
    //                                     <th>Shift</th>
    //                                     <th>In Time</th>
    //                                     <th>Out Time</th>
    //                                     <th>Late Min</th>
    //                                     <th>OT Hr:Mi</th>
    //                                     <th>DOT Hr:Mi</th>
    //                                     <th>TOT Hr:Mi</th>
    //                                     <th>Leave Type</th>
    //                                     <th>Leave Day</th>
    //                                 </tr>
    //                                 '.  $tablebody .'
    //                             </table>
    //                         </body>
    //                         </html>';
    //                         $pdf = PDF::loadHTML($html);
    //                         $pdf->setPaper('A4', 'portrait');
    //     }

       
    //     return $pdf->download('Employee Attedance Report.pdf');

    // }

    public function generatereport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );
    
        $employees = DB::table('employees')
            ->select(
                'employees.id', 
                'employees.emp_id', 
                'employees.emp_fullname', 
                'employees.emp_gender',
                'departments.name AS departmentname',
                'job_titles.title AS jobtitlename',
                'shift_types.shift_name AS shiftname'
            )
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
            ->where('employees.deleted', 0)
            ->where('employees.emp_department', $department)
            ->whereBetween('attendances.date', [$from_date, $to_date]) // Filter employees with attendance in range
            ->groupBy('employees.id')
            ->orderBy('employees.id')
            ->limit(20)
            ->get();
    
        $pdfData = [];
    
        foreach ($employees as $employee) {
            $attendanceData = [];
    
            foreach ($period as $date) {
                $currentDate = $date->format('Y-m-d');
                $dayType = in_array($date->format('l'), ['Saturday', 'Sunday']) ? $date->format('l') : 'Weekday';
    
                $attendance = DB::table('attendances')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('date', $currentDate)
                    ->selectRaw('MIN(timestamp) as in_time, MAX(timestamp) as out_time, MAX(date) as max_date')
                    ->first();
    
                $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
                $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';
    
                $shiftCheck = DB::table('employeeshiftdetails')
                    ->where('emp_id', $employee->id)
                    ->whereDate('date_from', '<=', $currentDate)
                    ->whereDate('date_to', '>=', $currentDate)
                    ->exists();
                
                $shift_name = $shiftCheck ? 'Night Shift' : $employee->shiftname;
    
                $lateMinutes = DB::table('employee_late_attendance_minites')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('attendance_date', $currentDate)
                    ->value('minites_count') ?? 0;
    
                $otApproved = DB::table('ot_approved')
                    ->where('emp_id', $employee->emp_id)
                    ->whereDate('date', $currentDate)
                    ->select('hours', 'double_hours', 'triple_hours')
                    ->first();
    
                $leave = DB::table('leaves')
                    ->select('leave_types.leave_type AS leavename', 'leaves.no_of_days')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->where('emp_id', $employee->id)
                    ->whereDate('leave_from', '<=', $currentDate)
                    ->whereDate('leave_to', '>=', $currentDate)
                    ->where('status', 'Approved')
                    ->first();
    
                $holiday = DB::table('holidays')
                    ->where('date', $currentDate)
                    ->value('holiday_name');
    
                $attendanceData[] = [
                    'in_date' => $currentDate,
                    'out_date' => $attendance->max_date ?? ' ',
                    'day_type' => $holiday ?? $dayType,
                    'shift' => $shift_name,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'late_min' => $lateMinutes,
                    'ot_hours' => $otApproved->hours ?? '0:00',
                    'double_ot' => $otApproved->double_hours ?? '0:00',
                    'triple_ot' => $otApproved->triple_hours ?? '0:00',
                    'leave_type' => $leave->leavename ?? '',
                    'leave_days' => $leave->no_of_days ?? '',
                ];
            }
    
            $pdfData[] = [
                'employee' => $employee,
                'attendance' => $attendanceData,
            ];
        }

        ini_set("memory_limit", "999M");
		ini_set("max_execution_time", "999");

        $pdf = Pdf::loadView('Report.attendaceemployeereportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
        return $pdf->download('Employee Attedance Report.pdf');
    }
    
    // public function generatereport(Request $request) {
    //     $department = $request->get('department');
    //     $from_date = $request->get('from_date');
    //     $to_date = $request->get('to_date');
    
    //     $period = new DatePeriod(
    //         new DateTime($from_date),
    //         new DateInterval('P1D'), 
    //         new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
    //     );
    
    //     $reportPath = public_path('reports'); // Change path to 'public/reports/'
        
    //     // Ensure 'reports' directory exists
    //     if (!file_exists($reportPath)) {
    //         mkdir($reportPath, 0777, true);
    //     }
    
    //     $pdfFiles = [];
    //     $zipFilePath = $reportPath . '/Employee_Attendance_Reports.zip';
        
    //     // Delete old ZIP file if it exists
    //     if (file_exists($zipFilePath)) {
    //         unlink($zipFilePath);
    //     }
    
    //     // Fetch employees in batches of 5
    //     DB::table('employees')
    //         ->select(
    //             'employees.id', 
    //             'employees.emp_id', 
    //             'employees.emp_fullname', 
    //             'employees.emp_gender',
    //             'departments.name AS departmentname',
    //             'job_titles.title AS jobtitlename',
    //             'shift_types.shift_name AS shiftname'
    //         )
    //         ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
    //         ->leftJoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
    //         ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
    //         ->leftJoin('attendances', 'employees.emp_id', '=', 'attendances.emp_id')
    //         ->where('employees.deleted', 0)
    //         ->where('employees.emp_department', $department)
    //         ->whereBetween('attendances.date', [$from_date, $to_date]) // Filter employees with attendance in range
    //         ->groupBy('employees.id')
    //         ->orderBy('employees.id')
    //         ->chunk(5, function ($employees) use ($period, &$pdfFiles, $reportPath) {
    //             $pdfData = [];
    
    //             foreach ($employees as $employee) {
    //                 $attendanceData = [];
    
    //                 foreach ($period as $date) {
    //                     $currentDate = $date->format('Y-m-d');
    //                     $dayType = in_array($date->format('l'), ['Saturday', 'Sunday']) ? $date->format('l') : 'Weekday';
    
    //                     $attendance = DB::table('attendances')
    //                         ->where('emp_id', $employee->emp_id)
    //                         ->whereDate('date', $currentDate)
    //                         ->selectRaw('MIN(timestamp) as in_time, MAX(timestamp) as out_time, MAX(date) as max_date')
    //                         ->first();
    
    //                     $inTime = $attendance->in_time ? date('H:i:s', strtotime($attendance->in_time)) : ' ';
    //                     $outTime = $attendance->out_time ? date('H:i:s', strtotime($attendance->out_time)) : ' ';
    
    //                     $shiftCheck = DB::table('employeeshiftdetails')
    //                         ->where('emp_id', $employee->id)
    //                         ->whereDate('date_from', '<=', $currentDate)
    //                         ->whereDate('date_to', '>=', $currentDate)
    //                         ->exists();
                        
    //                     $shift_name = $shiftCheck ? 'Night Shift' : $employee->shiftname;
    
    //                     $lateMinutes = DB::table('employee_late_attendance_minites')
    //                         ->where('emp_id', $employee->emp_id)
    //                         ->whereDate('attendance_date', $currentDate)
    //                         ->value('minites_count') ?? 0;
    
    //                     $lateMinutes = is_numeric($lateMinutes) ? (int) $lateMinutes : 0;
    
    //                     $otApproved = DB::table('ot_approved')
    //                         ->where('emp_id', $employee->emp_id)
    //                         ->whereDate('date', $currentDate)
    //                         ->select('hours', 'double_hours', 'triple_hours')
    //                         ->first();
    
    //                     $otHours = isset($otApproved->hours) && is_numeric($otApproved->hours) ? (float) $otApproved->hours : 0.0;
    //                     $doubleOt = isset($otApproved->double_hours) && is_numeric($otApproved->double_hours) ? (float) $otApproved->double_hours : 0.0;
    //                     $tripleOt = isset($otApproved->triple_hours) && is_numeric($otApproved->triple_hours) ? (float) $otApproved->triple_hours : 0.0;
    
    //                     $leave = DB::table('leaves')
    //                         ->select('leave_types.leave_type AS leavename', 'leaves.no_of_days')
    //                         ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
    //                         ->where('emp_id', $employee->id)
    //                         ->whereDate('leave_from', '<=', $currentDate)
    //                         ->whereDate('leave_to', '>=', $currentDate)
    //                         ->where('status', 'Approved')
    //                         ->first();
    
    //                     $holiday = DB::table('holidays')
    //                         ->where('date', $currentDate)
    //                         ->value('holiday_name');
    
    //                     $attendanceData[] = [
    //                         'in_date' => $currentDate,
    //                         'out_date' => $attendance->max_date ?? ' ',
    //                         'day_type' => $holiday ?? $dayType,
    //                         'shift' => $shift_name,
    //                         'in_time' => $inTime,
    //                         'out_time' => $outTime,
    //                         'late_min' => $lateMinutes,
    //                         'ot_hours' => $otHours,
    //                         'double_ot' => $doubleOt,
    //                         'triple_ot' => $tripleOt,
    //                         'leave_type' => $leave->leavename ?? '',
    //                         'leave_days' => $leave->no_of_days ?? '',
    //                     ];
    //                 }
    
    //                 $pdfData[] = [
    //                     'employee' => $employee,
    //                     'attendance' => $attendanceData,
    //                 ];
    //             }
    
    //             // Generate individual PDF for each batch
    //             $batchNumber = count($pdfFiles) + 1;
    //             $fileName = "Employee_Attendance_Report_Batch_$batchNumber.pdf";
    //             $filePath = $reportPath . '/' . $fileName;
    
    //             ini_set("memory_limit", "999M");
    //             ini_set("max_execution_time", "999");
    
    //             $pdf = Pdf::loadView('Report.attendaceemployeereportPDF', compact('pdfData'))->setPaper('A4', 'portrait');
    //             $pdf->save($filePath);
    
    //             $pdfFiles[] = $filePath;
    //         });
    
    //     // Create ZIP archive
    //     $zip = new ZipArchive;
    //     if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
    //         foreach ($pdfFiles as $file) {
    //             $zip->addFile($file, basename($file));
    //         }
    //         $zip->close();
    //     }
    
    //     // Return ZIP file for download
    //     return response()->download($zipFilePath)->deleteFileAfterSend(true);
    // }
    
    
}
