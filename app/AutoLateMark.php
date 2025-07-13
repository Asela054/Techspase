<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateInterval;
use DateTime;

class AutoLateMark extends Model
{
    public function auto_late_attendace_mark($todate, $company_id, $department_id){

        $date = Carbon::parse($todate)->format('Y-m-d');
    
            $employees = DB::table('employees')
                ->leftjoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
                ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
                ->select('employees.emp_id','employees.emp_name_with_initial','shift_types.onduty_time','job_categories.late_type','job_categories.short_leaves','job_categories.half_days','job_categories.late_attend_min')
                ->where('employees.deleted', 0) 
                ->where('employees.emp_company', $company_id) 
                ->where('employees.emp_department', $department_id) 
                ->get();

            $lateAttendanceData = [];

            foreach($employees as $employee){
                $emp_id = $employee->emp_id; 
                $emp_name = $employee->emp_name_with_initial; 
                $shiftonduty_time = Carbon::parse($date.' '.$employee->onduty_time);

                    $nightshiftcheck = DB::table('employeeshiftdetails')
                        ->leftjoin('shift_types', 'employeeshiftdetails.shift_id', '=', 'shift_types.id')
                        ->select('shift_types.onduty_time')
                        ->whereDate('date_from', '<=', $date)
                        ->whereDate('date_to', '>=', $date)
                        ->where('emp_id', $emp_id)
                        ->first();

                    if($nightshiftcheck){
                        $shiftonduty_time = Carbon::parse($date.' '.$nightshiftcheck->onduty_time);
                    }

                    $attendance = DB::table('attendances as at1')
                            ->select(['at1.*',
                                DB::raw('MAX(at1.timestamp) as lasttimestamp'),
                                DB::raw('MIN(at1.timestamp) as firsttimestamp')
                            ])
                            ->where('at1.emp_id', $emp_id)
                            ->where('date', $date)
                            ->whereNull('at1.deleted_at')
                            ->first();


                if ($attendance->id != null) {
                
                    $attendanceid = $attendance->id;
                   $attendacedate = Carbon::parse($attendance->date)->format('Y-m-d');

                    $firsttimestamp = Carbon::parse($attendance->firsttimestamp);
                    $lasttimestamp = Carbon::parse($attendance->lasttimestamp);

                    $workhours = $lasttimestamp->diff($firsttimestamp)->format('%H:%I:%S');
                    $ondutyTime = Carbon::parse($shiftonduty_time);

                    $interval = $firsttimestamp->diff($ondutyTime);
                    $minutesDifference = ($interval->h * 60) + $interval->i + ($interval->s / 60);
                    $minutesDifference = round($minutesDifference, 1);

                    if ($firsttimestamp > $ondutyTime) {
                        $interval = $firsttimestamp->diff($ondutyTime);
                         $minutesDifference = ($interval->h * 60) + $interval->i + ($interval->s / 60);
                          $minutesDifference = round($minutesDifference, 1);

                        if($minutesDifference > 0){
                            $lateAttendanceData[] = [
                                'attendance_id' => $attendanceid,
                                'emp_id' => $emp_id,
                                'emp_name' => $emp_name,
                                'attendance_date' => $attendacedate,
                                'minites_count' => $minutesDifference,
                                'check_in_time' => $firsttimestamp->format('H:i:s'),
                                'check_out_time' => $lasttimestamp->format('H:i:s'),
                                'working_hours' => $workhours
                            ];
                        }
                    }
                }
            }

            return $lateAttendanceData;

    }
}
