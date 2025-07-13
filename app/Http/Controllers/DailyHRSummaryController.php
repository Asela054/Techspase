<?php

namespace App\Http\Controllers;

use App\DailyHRSummary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Datatables;
use Carbon\Carbon;

class DailyHRSummaryController extends Controller
{
     public function index()
    {
        $permission = Auth::user()->can('daily-summary-list');
        if (!$permission) {
            abort(403);
        }
        return view('dailyhrsummary.dailysummary');
    }

     public function insert(Request $request)
      {
        $permission = Auth::user()->can('daily-summary-create');
        if (!$permission) {
            abort(403);
        }

    
        $current_date_time = Carbon::now()->toDateTimeString();
        $summarydate = $request->input('summarydate');

        $attendacecount = (new \App\DailyHRSummary)->get_attendancecount($summarydate);
        $leavecount = (new \App\DailyHRSummary)->get_leavecount($summarydate);
        $nopaycount = (new \App\DailyHRSummary)->get_nopaycount($summarydate);
        $latecount = (new \App\DailyHRSummary)->get_latecount($summarydate);

        $absentcount = $leavecount + $nopaycount;

        $existingRecord = DailyHRSummary::where('date', $summarydate)
                                   ->where('status', '1')
                                   ->first();

                if ($existingRecord) {

                $existingRecord->update([
                    'attendace_count' => $attendacecount,
                    'absent_count' => $absentcount,
                    'leave_count' => $leavecount,
                    'nopay_count' => $nopaycount,
                    'late_count' => $latecount,
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time
                ]);
                $message = 'Daily HR Summary is successfully Updated';

            } else {
                // Create new record
                $hrsummary = new DailyHRSummary();
                $hrsummary->date = $summarydate;
                $hrsummary->attendace_count = $attendacecount;
                $hrsummary->absent_count = $absentcount;
                $hrsummary->leave_count = $leavecount; 
                $hrsummary->nopay_count = $nopaycount;
                $hrsummary->late_count = $latecount;
                $hrsummary->status = '1';
                $hrsummary->created_by = Auth::id();
                $hrsummary->created_at = $current_date_time;
                $hrsummary->save();

                $message = 'Daily HR Summary is successfully Inserted';
            }
            return response()->json(['success' => $message]);
      }

       public function daliysummarylist()
    {
        $dailysuimmary = DB::table('daliy_hrsummary')
        ->select('daliy_hrsummary.*')
        ->where('status', 1)
        ->get();
        return Datatables::of($dailysuimmary)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';

           $permission = Auth::user()->can('daily-summary-delete');
                if ($permission) {
                  $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }
   
            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

     public function delete(Request $request){
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        DailyHRSummary::where('id',$id)
        ->update($form_data);

          return response()->json(['success' => 'Daily HR Summary is Successfully Deleted']);
    }
}
