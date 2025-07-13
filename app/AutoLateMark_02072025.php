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
    public function auto_late_attendace_mark(){

        $date = Carbon::yesterday()->format('Y-m-d');

        
        // $employees = DB::table('employees')
        //             ->leftjoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
        //             ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
        //             ->select('employees.emp_id','shift_types.onduty_time','job_categories.late_type','job_categories.short_leaves','job_categories.half_days','job_categories.late_attend_min')
        //             ->where('employees.deleted', 0) 
        //             ->get();

        //     foreach( $employees as  $employee){

        //         $emp_id = $employee->emp_id; 
        //         $shiftonduty_time = Carbon::parse($employee->onduty_time);

        //         $latetype = $employee->late_type; 
        //         $shortleave = $employee->short_leaves; 
        //         $halfday = $employee->half_days;   
        //         $minitescount = $employee->late_attend_min; 


        //         $attendance = DB::table('attendances as at1')
        //                         ->select(['at1.*',
        //                             DB::raw('MAX(at1.timestamp) as lasttimestamp'),
        //                             DB::raw('MIN(at1.timestamp) as firsttimestamp')
        //                         ])
        //                         ->where('at1.emp_id', $emp_id)
        //                         ->where('date', $date)
        //                         ->whereNull('at1.deleted_at')
        //                         ->first();
        //         if ($attendance) {

        //             $attendanceid = $attendance->id;
        //             $attendacedate = $attendance->date;

        //             $firsttimestamp = Carbon::parse($attendance->firsttimestamp);
        //             $lasttimestamp = Carbon::parse($attendance->lasttimestamp);
        //             $workhours = $lasttimestamp->diff($firsttimestamp)->format('%H:%I:%S');
        //             $ondutyTime = Carbon::parse($shiftonduty_time);


        //             $interval = $firsttimestamp->diff($ondutyTime);
        //             $minutesDifference = ($interval->h * 60) + $interval->i;

        //         // Check if check-in time is after on-duty time
        //         if ($firsttimestamp > $ondutyTime) {
        //             $interval = $firsttimestamp->diff($ondutyTime);
        //             $minutesDifference = ($interval->h * 60) + $interval->i;

        //             $late_minutes_data[] = array(
        //                 'attendance_id' =>$attendanceid,
        //                 'emp_id' => $emp_id,
        //                 'attendance_date' => $attendacedate,
        //                 'minites_count' => $minutesDifference,
        //             );
        //         }

        //         $lateattedance_arry[] = array(
        //             'attendance_id' => $attendanceid,
        //             'emp_id' => $emp_id,
        //             'date' => $attendacedate,
        //             'check_in_time' =>$firsttimestamp ,
        //             'check_out_time' => $lasttimestamp,
        //             'working_hours' =>  $workhours,
        //             'created_by' => Auth::id(),
        //         );

        //         DB::table('employee_late_attendances')
        //         ->where('attendance_id', $attendanceid)
        //         ->where('emp_id', $emp_id)
        //         ->where('date', $attendacedate)
        //         ->delete();


        //         if (!empty($lateattedance_arry)) {
        //             DB::table('employee_late_attendances')->insert($lateattedance_arry);
        //         }

        //         if (!empty($late_minutes_data)) {
        //             DB::table('employee_late_attendance_minites')->insert($late_minutes_data);
        //         }


        //           //count this month leaves and to leaves table
        //         $leave_count = DB::table('employee_late_attendances')
        //         ->where('date', $date)
        //         ->where('emp_id', $emp_id)
        //         ->count();


        //         switch (true) {
        //             case ($leave_count == 1 || $leave_count == 2):
        //                 //add short leave
        //                 $half_short = 0.25;
        //                 break;
        //             default:
        //                 //add half day
        //                 $half_short = 0.5;
        //         }


        //         if($latetype == 1){

        //             if(!empty($minitescount)){
    
                       
        
        //                 $totalMinutes = DB::table('employee_late_attendance_minites')
        //                                     ->where('emp_id',  $emp_id) 
        //                                     ->whereRaw("DATE_FORMAT(attendance_date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')", [$date])
        //                                     ->where('attendance_date', '!=', $date) 
        //                                     ->sum('minites_count');
                        
        //                 $attendanceminitesrecord = DB::table('employee_late_attendance_minites')
        //                                             ->select('id', 'attendance_id', 'emp_id', 'attendance_date', 'minites_count')
        //                                             ->where('emp_id', $emp_id)
        //                                             ->where('attendance_date', '!=',$date)
        //                                             ->first();
                        
        //                 $totalminitescount = $totalMinutes + $attendanceminitesrecord->minites_count;
        
        //                 if( $minitescount < $totalminitescount){
        //                     $leave = new Leave;
        //                     $leave->emp_id =  $emp_id;
        //                     $leave->leave_type = 1;
        //                     $leave->leave_from = $date;
        //                     $leave->leave_to = $date;
        //                     $leave->no_of_days = '0';
        //                     $leave->half_short = '0';
        //                     $leave->reson = 'Late';
        //                     $leave->comment = '';
        //                     $leave->emp_covering = '';
        //                     $leave->leave_approv_person = Auth::id();
        //                     $leave->status = 'Pending';
        //                     $leave->save();
        //                 }
        //                 else{
        
        //                     $leave = new Leave;
        //                     $leave->emp_id = $emp_id;
        //                     $leave->leave_type = 1;
        //                     $leave->leave_from =  $date;
        //                     $leave->leave_to =  $date;
        //                     $leave->no_of_days = $half_short;
        //                     $leave->half_short = $half_short;
        //                     $leave->reson = 'Late';
        //                     $leave->comment = '';
        //                     $leave->emp_covering = '';
        //                     $leave->leave_approv_person = Auth::id();
        //                     $leave->status = 'Pending';
        //                     $leave->save();
            
        //                 }
                        
        //             }
                  
        //         }
        //         elseif($latetype == 2){
    
        //             if($leave_count <=  $shortleave)
        //             {
        //                 $leaveamount = 0.25;
        //                 $applyleavetype = 1;
        //             }
        //             elseif($leave_count <=  $halfday)
        //             {
        //                 $leaveamount = 0.5;
        //                 $applyleavetype = 1;
        //             }
        //             else{
        //                 $leaveamount = 0.5;
        //                 $applyleavetype = 3;
        //             }
    
    
        //             $leave = new Leave;
        //             $leave->emp_id = $emp_id;
        //             $leave->leave_type =  $applyleavetype;
        //             $leave->leave_from =  $date;
        //             $leave->leave_to = $date;
        //             $leave->no_of_days = $leaveamount;
        //             $leave->half_short = $leaveamount;
        //             $leave->reson = 'Late';
        //             $leave->comment = '';
        //             $leave->emp_covering = '';
        //             $leave->leave_approv_person = Auth::id();
        //             $leave->status = 'Pending';
        //             $leave->save();
    
    
        //         }
        //         elseif($latetype == 3){
    
        //             if($leave_count <=  $shortleave)
        //             {
        //                 $leaveamount = 0.25;
        //             }
        //             elseif($leave_count <=  $halfday)
        //             {
        //                 $leaveamount = 0.5;
        //             }
        //             else{
                       
        //                 if(!empty($minitescount)){
    
        //                         $leave = new Leave;
        //                         $leave->emp_id = $emp_id;
        //                         $leave->leave_type = 1;
        //                         $leave->leave_from = $date;
        //                         $leave->leave_to = $date;
        //                         $leave->no_of_days = '0';
        //                         $leave->half_short = '0';
        //                         $leave->reson = 'Late';
        //                         $leave->comment = '';
        //                         $leave->emp_covering = '';
        //                         $leave->leave_approv_person = Auth::id();
        //                         $leave->status = 'Pending';
        //                         $leave->save();
        //                 }
    
        //             }
        //         }

        //         }
        //     }

        //     return true; 

    }

    public function auto_late_attendace_mark_manual($todate, $company_id, $department_id){
        $date = Carbon::parse($todate)->format('Y-m-d');
        
        $employees = DB::table('employees')
            ->leftjoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->select('employees.emp_id','shift_types.onduty_time','job_categories.late_type','job_categories.short_leaves','job_categories.half_days','job_categories.late_attend_min')
            ->where('employees.deleted', 0) 
            ->where('employees.emp_company', $company_id) 
            ->where('employees.emp_department', $department_id) 
            ->get();

        foreach( $employees as  $employee){
            // if($employee->emp_id==1984){
                $late_minutes_data = array();
                $lateattedance_arry = array();

                $emp_id = $employee->emp_id; 
                $shiftonduty_time = Carbon::parse($date.' '.$employee->onduty_time);

                $latetype = $employee->late_type; 
                $shortleave = $employee->short_leaves; 
                $halfday = $employee->half_days;   
                $minitescount = $employee->late_attend_min; 

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

                if ($attendance->id!= null) {
                    $attendanceid = $attendance->id;
                    $attendacedate = $attendance->date;

                    $firsttimestamp = Carbon::parse($attendance->firsttimestamp);
                    $lasttimestamp = Carbon::parse($attendance->lasttimestamp);
                    $workhours = $lasttimestamp->diff($firsttimestamp)->format('%H:%I:%S');
                    $ondutyTime = Carbon::parse($shiftonduty_time);

                    $interval = $firsttimestamp->diff($ondutyTime);
                    $minutesDifference = ($interval->h * 60) + $interval->i;

                    // Check if check-in time is after on-duty time
                    if ($firsttimestamp > $ondutyTime) {
                        $interval = $firsttimestamp->diff($ondutyTime);
                        $minutesDifference = ($interval->h * 60) + $interval->i;

                        if($minutesDifference>0){
                            $late_minutes_data[] = array(
                                'attendance_id' =>$attendanceid,
                                'emp_id' => $emp_id,
                                'attendance_date' => $attendacedate,
                                'minites_count' => $minutesDifference,
                            );

                            $lateattedance_arry[] = array(
                                'attendance_id' => $attendanceid,
                                'emp_id' => $emp_id,
                                'date' => $attendacedate,
                                'check_in_time' =>$firsttimestamp ,
                                'check_out_time' => $lasttimestamp,
                                'working_hours' =>  $workhours,
                                'created_by' => Auth::id(),
                                'is_approved' =>  '1',
                                'approved_by' => Auth::id(),
                                'approved_at' => Carbon::now(),
                            );
                        }
                    }
                    
                    DB::table('employee_late_attendances')
                        ->where('attendance_id', $attendanceid)
                        ->where('emp_id', $emp_id)
                        ->where('date', $attendacedate)
                        ->delete();

                    DB::table('employee_late_attendance_minites')
                        ->where('attendance_id', $attendanceid)
                        ->where('emp_id', $emp_id)
                        ->delete();

                    if (!empty($lateattedance_arry)) {
                        DB::table('employee_late_attendances')->insert($lateattedance_arry);
                    }

                    if (!empty($late_minutes_data)) {
                        DB::table('employee_late_attendance_minites')->insert($late_minutes_data);
                    }


                    //count this month leaves and to leaves table
                    $leave_count = DB::table('employee_late_attendances')
                        ->where('date', $date)
                        ->where('emp_id', $emp_id)
                        ->count();


                    switch (true) {
                        case ($leave_count == 1 || $leave_count == 2):
                            //add short leave
                            $half_short = 0.25;
                            break;
                        default:
                            //add half day
                            $half_short = 0.5;
                    }

                    if($latetype == 1){
                        if(!empty($minitescount)){
                            $totalMinutes = DB::table('employee_late_attendance_minites')
                                ->where('emp_id',  $emp_id) 
                                ->whereRaw("DATE_FORMAT(attendance_date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')", [$date])
                                ->where('attendance_date', '!=', $date) 
                                ->sum('minites_count');
                                
                            $attendanceminitesrecord = DB::table('employee_late_attendance_minites')
                                ->select('id', 'attendance_id', 'emp_id', 'attendance_date', 'minites_count')
                                ->where('emp_id', $emp_id)
                                ->where('attendance_date',$date)
                                ->first();
                            
                            if($attendanceminitesrecord){
                                $totalminitescount = $totalMinutes + $attendanceminitesrecord->minites_count;
                            }else{
                                $totalminitescount = $totalMinutes;
                            }
            
                            if( $minitescount < $totalminitescount){
                                $leave = new Leave;
                                $leave->emp_id =  $emp_id;
                                $leave->leave_type = 1;
                                $leave->leave_from = $date;
                                $leave->leave_to = $date;
                                $leave->no_of_days = '0';
                                $leave->half_short = '0';
                                $leave->reson = 'Late';
                                $leave->comment = '';
                                $leave->emp_covering = '';
                                $leave->leave_approv_person = Auth::id();
                                $leave->status = 'Pending';
                                $leave->save();
                            }
                            else{
            
                                $leave = new Leave;
                                $leave->emp_id = $emp_id;
                                $leave->leave_type = 1;
                                $leave->leave_from =  $date;
                                $leave->leave_to =  $date;
                                $leave->no_of_days = $half_short;
                                $leave->half_short = $half_short;
                                $leave->reson = 'Late';
                                $leave->comment = '';
                                $leave->emp_covering = '';
                                $leave->leave_approv_person = Auth::id();
                                $leave->status = 'Pending';
                                $leave->save();
                
                            }
                            
                        }
                    
                    }
                    elseif($latetype == 2){
        
                        if($leave_count <=  $shortleave)
                        {
                            $leaveamount = 0.25;
                            $applyleavetype = 1;
                        }
                        elseif($leave_count <=  $halfday)
                        {
                            $leaveamount = 0.5;
                            $applyleavetype = 1;
                        }
                        else{
                            $leaveamount = 0.5;
                            $applyleavetype = 3;
                        }
        
        
                        $leave = new Leave;
                        $leave->emp_id = $emp_id;
                        $leave->leave_type =  $applyleavetype;
                        $leave->leave_from =  $date;
                        $leave->leave_to = $date;
                        $leave->no_of_days = $leaveamount;
                        $leave->half_short = $leaveamount;
                        $leave->reson = 'Late';
                        $leave->comment = '';
                        $leave->emp_covering = '';
                        $leave->leave_approv_person = Auth::id();
                        $leave->status = 'Pending';
                        $leave->save();
        
        
                    }
                    elseif($latetype == 3){
        
                        if($leave_count <=  $shortleave)
                        {
                            $leaveamount = 0.25;
                        }
                        elseif($leave_count <=  $halfday)
                        {
                            $leaveamount = 0.5;
                        }
                        else{
                        
                            if(!empty($minitescount)){
        
                                    $leave = new Leave;
                                    $leave->emp_id = $emp_id;
                                    $leave->leave_type = 1;
                                    $leave->leave_from = $date;
                                    $leave->leave_to = $date;
                                    $leave->no_of_days = '0';
                                    $leave->half_short = '0';
                                    $leave->reson = 'Late';
                                    $leave->comment = '';
                                    $leave->emp_covering = '';
                                    $leave->leave_approv_person = Auth::id();
                                    $leave->status = 'Pending';
                                    $leave->save();
                            }
        
                        }
                    }
                }
            // }
        }

        return true; 

    }
}
