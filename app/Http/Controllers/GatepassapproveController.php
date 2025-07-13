<?php

namespace App\Http\Controllers;

use App\Gatepass;
use App\Gatepassapproved;
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
        ->leftjoin('employees', 'gate_pass.emp_id', '=', 'employees.emp_id')
        ->select('gate_pass.*','employees.emp_name_with_initial As emp_name')
        ->whereIn('gate_pass.status', [1, 2])
        ->where('employees.deleted', 0)
        ->where('employees.status', 1)
        ->where('employees.is_resigned', 0)
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
        $currentTime = Carbon::now();

        if (!empty($data_array)) {
            $groupedData = [];
            foreach ($data_array as $row) {
                $key = $row['emp_id'] . '_' . $row['date'];
                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = [
                        'emp_id' => $row['emp_id'],
                        'date' => $row['date'],
                        'minites_count' => 0,
                        'entries' => []
                    ];
                }
                $groupedData[$key]['minites_count'] += $row['minites_count'];
                $groupedData[$key]['entries'][] = $row;
            }

            foreach ($data_array as $row) {
                $id = $row['rowid'];
                $data = array(
                    'approve_status' => 1,
                    'approved_by' => Auth::id(),
                    'updated_at' => $currentTime
                );
                Gatepass::where('id', $id)->update($data);
            }

            foreach ($groupedData as $group) {
                $firstEntry = $group['entries'][0];
                
                $existing = Gatepassapproved::where('emp_id', $firstEntry['emp_id'])
                                        ->where('date', $firstEntry['date'])
                                        ->first();

                if ($existing) {
                    $existing->update([
                        'minites_count' => $existing->minites_count + $group['minites_count'],
                        'updated_by' => Auth::id(),
                        'updated_at' => $currentTime
                    ]);
                } else {
                    Gatepassapproved::create([
                        'emp_id' => $firstEntry['emp_id'],
                        'date' => $firstEntry['date'],
                        'minites_count' => $group['minites_count'],
                        'status' => 1, 
                        'approve_status' => 1,
                        'created_by' => Auth::id(),
                        'approved_by' => Auth::id(),
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime
                    ]);
                }
            }
        }
        return response()->json(['success' => 'Gate Pass is successfully Approved']);
    }
    
}
