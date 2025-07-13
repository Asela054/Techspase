<?php

namespace App\Http\Controllers;

use App\Gatepass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Datatables;
use Carbon\Carbon;

class GatepassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        // $permission = $user->can('Leave-Deduction-list');

        // if(!$permission) {
        //     abort(403);
        // }

        $employees=DB::table('employees')->select('id','emp_name_with_initial','emp_job_code')->where('deleted',0)->get();
        return view('Gatepass.gatepass', compact('employees'));
    }

    public function insert(Request $request){


        $date = $request->input('date');
        $employeeID = $request->input('employeeID');
        $ontime = $request->input('ontime');
        $offtime = $request->input('offtime');
        $hidden_id = $request->input('hidden_id');
        $action = $request->input('action');

        if(!empty($offtime)){
            $ontime = Carbon::parse($ontime); 
            $offtime = Carbon::parse($offtime); 
            $minutes = $ontime->diffInMinutes($offtime);
        }else{
            $minutes = 0;
        }
      
        if( $action == 1){

            $gatepass = new Gatepass();
            $gatepass->emp_id = $employeeID;
            $gatepass->date = $date;
            $gatepass->intime = $ontime;
            $gatepass->offtime = $offtime;
            $gatepass->minites_count = $minutes;
            $gatepass->status = '1';
            $gatepass->approve_status = '0';
            $gatepass->created_by = Auth::id();
            $gatepass->updated_by = '0';
            $gatepass->approved_by = '0';
            $gatepass->save();

            $message = "Gate Pass Added successfully.";
        }else{

            $data = array(
                'emp_id' => $employeeID,
                'date' => $date,
                'intime' => $ontime,
                'offtime' => $offtime,
                'minites_count' => $minutes,
                'updated_by' => Auth::id(),
            );
        
            Gatepass::where('id', $hidden_id)
            ->update($data);

            $message = "Gate Pass Updated successfully.";
        }
        return response()->json(['success' => $message]);
    }

    public function gatepasslist()
    {
        $gatepass = DB::table('gate_pass')
        ->leftjoin('employees', 'gate_pass.emp_id', '=', 'employees.id')
        ->select('gate_pass.*','employees.emp_name_with_initial As emp_name')
        ->whereIn('gate_pass.status', [1, 2])
        ->where('gate_pass.approve_status', 0)
        ->get();
        return Datatables::of($gatepass)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            $btn = '';
           
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
   
            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function edit(Request $request)
    {
        // $permission = \Auth::user()->can('Job-Allocation-edit');
        // if (!$permission) {
        //     abort(403);
        // }

        $id = Request('id');
        if (request()->ajax()){

        $data = DB::table('gate_pass')
        ->select('gate_pass.*')
        ->where('gate_pass.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function delete(Request $request){
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Gatepass::where('id',$id)
        ->update($form_data);

          return response()->json(['success' => 'Gate Pass is Successfully Deleted']);
    }

    public function gatepass_csv(Request $request)
    {
        
        $file = $request->file('csv_file_u');
        $fileContents = file($file->getPathname());
        $firstRow = true;

        foreach ($fileContents as $line) 
        {
            if ($firstRow) {
                $firstRow = false;
                continue; 
            }

            $data = str_getcsv($line);

            if (count($data) < 4) {
                continue;
            }

            $emp_id = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[0]));
            $raw_date = trim($data[1]);
            $intime = trim($data[2]);
            $outtime = trim($data[3]);

            $date = \Carbon\Carbon::createFromFormat('m/d/Y H:i', $raw_date)->format('Y-m-d');

            $emp = DB::table('employees')
                ->select('emp_id')
                ->where('emp_id', $emp_id)
                ->first();

                if (!$emp) {
                    continue; 
                }

            $gatepass = new Gatepass();
            $gatepass->emp_id = $emp->id;
            $gatepass->date = $date;
            $gatepass->intime = $intime;
            $gatepass->offtime = $outtime;
            $gatepass->status = '1';
            $gatepass->created_by = Auth::id();
            $gatepass->updated_by = '0';
            $gatepass->approved_by = '0';
            $gatepass->save();

        }

        return response()->json(['status' => true, 'msg' => 'CSV Upload successfully.']);
    }
 
}
