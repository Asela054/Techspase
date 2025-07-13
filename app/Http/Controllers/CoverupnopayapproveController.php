<?php

namespace App\Http\Controllers;

use App\Coverup_detail;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Leave;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Datatables;
use Carbon\Carbon;


class CoverupnopayapproveController extends Controller
{
    public function index()
    {
        $permission = Auth::user()->can('Coverup-Approvel');
        if (!$permission) {
            abort(403);
        }
        $employee = Employee::orderBy('id', 'desc')->get();

        return view('Leave.coverupnopay', compact('employee'));
    }

    public function getabsetcoveringup(Request $request){
        $permission = \Auth::user()->can('Coverup-Approvel');
        if (!$permission) {
            abort(403);
        }
        
        $department=$request->input('department');
        $firstDate =  $request->input('from_date');
        $lastDate = $request->input('to_date');

        $datareturn = [];

        $query =  DB::table('coverup_details')
            ->leftjoin('employees as e', 'coverup_details.emp_id', '=', 'e.emp_id')
            ->select('coverup_details.*', 'e.emp_name_with_initial as emp_name','e.id as emp_autoid','e.emp_department','e.emp_id as empid')
            ->where('e.emp_department', '=', $department)
            ->whereBetween('date', [$firstDate, $lastDate])
            ->get();

            foreach ($query as $row) {
                $empId = $row->empid;
                $empName = $row->emp_name;
                $empautoid = $row->emp_autoid;
                $coveringdate = $row->date;
                $start_time = $row->start_time;
                $end_time = $row->end_time;
                $coveringhours = $row->covering_hours;
                
                $attendance = DB::table('attendances')
                ->select('attendances.*')
                ->where('uid', $empId)
                ->where('date', $coveringdate)
                ->whereNull('deleted_at')
                ->first();

                if(!$attendance){

                    $datareturn[] = [
                        'empid' => $empId,
                        'emp_name' => $empName,
                        'emp_autoid' => $empautoid,
                        'covering_date' => $coveringdate,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'covering_hours' => $coveringhours
                    ];     
                }
              
            } 
            return response()->json([ 'data' => $datareturn ]);

    }

    public function approvecoveringnopay(Request $request){

        $permission = \Auth::user()->can('Coverup-Approvel');
        if (!$permission) {
            abort(403);
        }


        $dataarry = $request->input('dataarry');

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $epfno = $row['emp_name'];
            $covering_date = $row['covering_date'];
            $start_time = $row['start_time'];
            $end_time = $row['end_time'];
            $covering_hours = $row['covering_hours'];
            $emp_autoid = $row['emp_autoid'];


            $leave = Leave::where('emp_id', $empid)
              ->where('leave_type', '3')
              ->whereDate('leave_from', '<=', $covering_date)
              ->whereDate('leave_to', '>=', $covering_date)
              ->first();
              if ($leave) {
                $leave->update([
                    'no_of_days' => '1',
                    'reson' => 'No Covering',
                    'leave_approv_person' => Auth::id(),
                    'status' => 'Approved',
                    'updated_at' =>$current_date_time
                ]);
            } else {  

            $leave = new Leave;
            $leave->emp_id = $empid;
            $leave->leave_type = '3';
            $leave->leave_from = $covering_date;
            $leave->leave_to = $covering_date;
            $leave->no_of_days = '1';
            $leave->half_short = '0';
            $leave->reson = 'No Covering';
            $leave->comment = '';
            $leave->emp_covering = '';
            $leave->leave_approv_person = Auth::id();
            $leave->status = 'Approved';
            $leave->save();
            }
        }
        return response()->json(['success' => 'Covering Nopay is successfully Approved']);
    }
}
