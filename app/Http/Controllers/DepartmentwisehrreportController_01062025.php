<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DateTime;
use DB;

class DepartmentwisehrreportController extends Controller
{
     public function index()
    {
        $permission = Auth::user()->can('Leave-type-report');
        if (!$permission) {
            abort(403);
        }
        $leavetype = DB::table('leave_types')->select('*')->whereIn('id', [1, 2])->get();
        return view('departmetwise_reports.leavetypereport', compact('leavetype'));
    }

     public function generateleavetypereport(Request $request)
    {
        $department = $request->get('department');
        $leavetype = $request->get('leavetype');
        $currentYear = date('Y');
        $today = date('Y-m-d');

         $datareturn = [];

          $query =  DB::table('employees')
            ->select('emp_name_with_initial as emp_name','id as emp_autoid','emp_department','emp_id as empid','emp_join_date', 'emp_status')
            ->where('emp_department', '=', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->get();

              foreach ($query as $employee) {
        
                if ($leavetype == 1) { // Annual Leave
                  $totalAllocated = 14; 
                } 
                elseif ($leavetype == 2) { 
                        $totalAllocated = 7;
                }

              
                $totalTaken = DB::table('leaves')
                    ->where('emp_id', $employee->empid)
                    ->where('leave_type', $leavetype)
                    ->where('status', 'Approved')
                    ->whereYear('leave_from', $currentYear)
                    ->sum('no_of_days');

                $remainingLeaves = $totalAllocated - $totalTaken;

                $datareturn[] = [
                    'emp_id' => $employee->empid,
                    'emp_name' => $employee->emp_name,
                    'total_allocated' => $totalAllocated,
                    'total_taken' => $totalTaken,
                    'remaining_leaves' => max(0, $remainingLeaves),
                ];
            }

            return response()->json([
                'data' => $datareturn,
                'status' => 'success'
            ]);

    }

     public function hrotreport()
    {
        $permission = Auth::user()->can('OT-60After-report');
        if (!$permission) {
            abort(403);
        }
        return view('departmetwise_reports.ot60afterreport');
    }

    public function generatehrotreport(Request $request){
        $department = $request->get('department');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

         $datareturn = [];

          $query =  DB::table('employees')
            ->select('emp_name_with_initial as emp_name','id as emp_autoid','emp_department','emp_id as empid','emp_join_date', 'emp_status')
            ->where('emp_department', '=', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->get();

            foreach ($query as $employee) {
                $normal_ot_hours = (new \App\OtApproved)->get_ot_hours_report($employee->empid, $from_date, $to_date);
                $double_ot_hours = (new \App\OtApproved)->get_double_ot_hours_report($employee->empid, $from_date, $to_date);
                $triple_ot_hours = (new \App\OtApproved)->get_triple_ot_hours_report($employee->empid, $from_date, $to_date);
                $holiday_ot_hours = (new \App\OtApproved)->get_holiday_ot_hours_report($employee->empid, $from_date, $to_date);
                $holiday_double_ot_hours = (new \App\OtApproved)->get_holiday_double_ot_hours_report($employee->empid, $from_date, $to_date);
                $sundaydouble_ot_hours = (new \App\OtApproved)->get_sundaydouble_ot_hours_report($employee->empid, $from_date, $to_date);
                $poyaextended_normal_othours = (new \App\OtApproved)->get_poyaextended_normal_othours_report($employee->empid, $from_date, $to_date);

                
                  $total_ot_hours = $normal_ot_hours
                    + $double_ot_hours
                    + $triple_ot_hours
                    + $holiday_ot_hours
                    + $holiday_double_ot_hours
                    + $sundaydouble_ot_hours
                    + $poyaextended_normal_othours;

                 $ot_hours_over_60 = max(0, $total_ot_hours - 60);  
                 
                 $datareturn[] = [
                    'emp_id' => $employee->empid,
                    'emp_name' => $employee->emp_name,
                    'total_ot_hours' => $total_ot_hours,
                    'ot_hours_over_60' => $ot_hours_over_60
                ];
            } 
            return response()->json([
                'data' => $datareturn,
                'status' => 'success'
            ]);
    }
}
