<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;
use App\Leave;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LateminitesautogenerateController extends Controller
{
    public function index(){

        $user = Auth::user();
        $permission = $user->can('Late-minites-manual-mark-list');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.lateminitesautomark');
    }

    public function marklateattendance(Request $request){

        $user = Auth::user();
        $permission = $user->can('Lateminites-Approvel-apprve');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $date = $request->get('closedate');
        $company_id = $request->get('company_id');
        $department_id = $request->get('department_id');

        try {
              $lateAttendanceData = (new \App\AutoLateMark)->auto_late_attendace_mark($date, $company_id, $department_id);
            
          return response()->json([
            'success' => 'Late Attendance successfully marked for ' . $date,
            'lateAttendance' => $lateAttendanceData
        ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['Failed to mark late attendance: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function applylateattendance(Request $request)
    {

        $permission = Auth::user()->can('Lateminites-Approvel-apprve');
        if (!$permission) {
            abort(403);
        }


        $dataarry = $request->input('dataarry');
         $closedate = $request->get('closedate');
          $date = Carbon::parse($closedate)->format('Y-m-d');

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $late_minutes_data = array();
            $lateattedance_arry = array();


            $empid = $row['empid'];
            $emp_name = $row['emp_name'];
            $attendacedate = $row['attendacedate'];
            $minites_count = $row['minites_count'];
            $check_in_time = $row['check_in_time'];
            $check_out_time = $row['check_out_time'];
            $working_hours = $row['working_hours'];
            $attendaceid = $row['attendaceid'];


             $late_minutes_data[] = array(
                'attendance_id' =>$attendaceid,
                'emp_id' => $empid,
                'attendance_date' => $attendacedate,
                'minites_count' => $minites_count,
            );

             $lateattedance_arry[] = array(
                'attendance_id' => $attendaceid,
                'emp_id' => $empid,
                'date' => $attendacedate,
                'check_in_time' =>$check_in_time ,
                'check_out_time' => $check_out_time,
                'working_hours' =>  $working_hours,
                'created_by' => Auth::id(),
                'is_approved' =>  '1',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            );


             DB::table('employee_late_attendances')
                ->where('attendance_id', $attendaceid)
                ->where('emp_id', $empid)
                ->where('date', $attendacedate)
                ->delete();

            DB::table('employee_late_attendance_minites')
                ->where('attendance_id', $attendaceid)
                ->where('emp_id', $empid)
                ->delete();

            if (!empty($lateattedance_arry)) {
                DB::table('employee_late_attendances')->insert($lateattedance_arry);
            }

            if (!empty($late_minutes_data)) {
                DB::table('employee_late_attendance_minites')->insert($late_minutes_data);
            }


             $employees = DB::table('employees')
            ->leftjoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->select('job_categories.late_type','job_categories.short_leaves','job_categories.half_days','job_categories.late_attend_min')
            ->where('employees.emp_id', $empid)
            ->first();

             $latetype = $employees->late_type; 
            $shortleave = $employees->short_leaves; 
            $halfday = $employees->half_days;   
            $minitescount = $employees->late_attend_min; 

            

            //count this month leaves and to leaves table
            $leave_count = DB::table('employee_late_attendances')
                ->where('date', $date)
                ->where('emp_id', $empid)
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
                        ->where('emp_id',  $empid) 
                        ->whereRaw("DATE_FORMAT(attendance_date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')", [$date])
                        ->where('attendance_date', '!=', $date) 
                        ->sum('minites_count');
                        
                    $attendanceminitesrecord = DB::table('employee_late_attendance_minites')
                        ->select('id', 'attendance_id', 'emp_id', 'attendance_date', 'minites_count')
                        ->where('emp_id', $empid)
                        ->where('attendance_date',$date)
                        ->first();
                    
                    if($attendanceminitesrecord){
                        $totalminitescount = $totalMinutes + $attendanceminitesrecord->minites_count;
                    }else{
                        $totalminitescount = $totalMinutes;
                    }
    
                    if( $minitescount < $totalminitescount){
                        $leave = new Leave();
                        $leave->emp_id =  $empid;
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
    
                        $leave = new Leave();
                        $leave->emp_id = $empid;
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


                $leave = new Leave();
                $leave->emp_id = $empid;
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

                            $leave = new Leave();
                            $leave->emp_id = $empid;
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
        return response()->json(['success' => 'Late Mark is successfully Inserted']);
    }

}
