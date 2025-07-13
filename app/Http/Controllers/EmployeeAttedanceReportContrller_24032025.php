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


    public function generatereport(Request $request) {
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $from_range = $request->get('from_range', 0);
        $to_range = $request->get('to_range', 20);
    
        $period = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'), 
            new DateTime(date('Y-m-d', strtotime($to_date . ' +1 day')))
        );
    
        $from_range = max(0, (int) $from_range);
        $to_range = max($from_range, (int) $to_range);
        $limit = $to_range - $from_range; 

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
            ->whereBetween('attendances.date', [$from_date, $to_date])
            ->groupBy('employees.id')
            ->orderBy('employees.emp_id')
            ->offset($from_range) 
            ->limit($limit)
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
                    ->select('hours', 'double_hours', 'triple_hours','holiday_normal_hours','holiday_double_hours','sunday_double_ot_hrs','poya_extended_normal_ot_hrs')
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
                    ->select('holiday_name','holiday_type')
                    ->first();

                    $otHours = $otApproved->hours ?? '0:00';
                    $doubleOT = $otApproved->double_hours ?? '0:00';
                    $tripleOT = $otApproved->triple_hours ?? '0:00';

                    if ($dayType == 'Sunday') {
                        $doubleOT = $otApproved->sunday_double_ot_hrs ?? '0:00';

                    } elseif ($holiday) {
                        $otHours = $otApproved->holiday_normal_hours ?? '0:00';
                        $doubleOT = $otApproved->holiday_double_hours ?? '0:00';

                        if ($holiday->holiday_type == 1) { 
                            $poyaExtendedHours = $otApproved->poya_extended_normal_ot_hrs ?? '0:00';
                            $regularHours = $otApproved->hours ?? '0:00';
                        
                            list($poyaHours, $poyaMinutes) = explode(':', $poyaExtendedHours);
                            list($regularHrs, $regularMins) = explode(':', $regularHours);
                        
                            $poyaExtendedHoursNumeric = (float)$poyaHours + ((float)$poyaMinutes / 60);
                            $regularHoursNumeric = (float)$regularHrs + ((float)$regularMins / 60);
                        
                            $otHoursNumeric = $poyaExtendedHoursNumeric + $regularHoursNumeric;
                        
                            $wholeHours = (int)$otHoursNumeric;
                            $decimalMinutes = ($otHoursNumeric - $wholeHours) * 60;
                            $otHours = sprintf('%02d:%02d', $wholeHours, $decimalMinutes);
                        }
                    }

                $attendanceData[] = [
                    'in_date' => $currentDate,
                    'out_date' => isset($attendance->max_date) ? \Carbon\Carbon::parse($attendance->max_date)->format('Y-m-d') : ' ',
                    'day_type' => $holiday->holiday_name ?? $dayType,
                    'shift' => $shift_name,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'late_min' => $lateMinutes,
                    'ot_hours' => $otHours,
                    'double_ot' => $doubleOT,
                    'triple_ot' => $tripleOT,
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
    
}
