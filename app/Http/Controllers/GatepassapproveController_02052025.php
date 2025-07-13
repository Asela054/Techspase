<?php

namespace App\Http\Controllers;

use App\Gatepass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class GatepassapproveController extends Controller
{
    public function index()
    {
        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code')->where('deleted',0)->get();
        return view('Gatepass.gatepass_approve', compact('employees'));
    }

    public function gatepass_approvelist()
    {
        $gatepass = DB::table('gate_pass')
        ->leftjoin('employees', 'gate_pass.emp_id', '=', 'employees.id')
        ->select('gate_pass.*','employees.emp_name_with_initial As emp_name')
        ->whereIn('gate_pass.status', [1, 2])
        ->get();
        return Datatables::of($gatepass)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';

                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
   
            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }


    public function approvegatepass(Request $request)
    {
        $data_array = $request->input('dataarry');

        if (!empty($data_array)) {
        foreach ($data_array as $row) {
            $id = $row['rowid'];
            $empname = $row['emp_name'];
            $date = $row['date'];
            $ontime = $row['ontime'];
            $offtime = $row['offtime'];

            $data = array(
                'approve_status' => 1,
                'approved_by' => Auth::id(),
            );
        
            Gatepass::where('id', $id)->update($data);
        }
       }
        return response()->json(['success' => 'Gate Pass is successfully Approved']);
    }
}
