<?php

namespace App\Http\Controllers;

use App\Gatepass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Datatables;
use Carbon\Carbon;
use Validator;

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
            $minutes = $offtime->diffInMinutes($ontime);
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
        ->leftjoin('employees', 'gate_pass.emp_id', '=', 'employees.emp_id')
        ->select('gate_pass.*','employees.emp_name_with_initial As emp_name')
        ->whereIn('gate_pass.status', [1, 2])
        ->where('gate_pass.approve_status', 0)
        ->where('employees.deleted', 0)
        ->where('employees.status', 1)
        ->where('employees.is_resigned', 0)
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
        $validator = Validator::make($request->all(), [
            'csv_file_u' => 'required|file|mimes:csv,txt',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $filename = $request->file('csv_file_u');
        $file = fopen($filename, 'r');

        $gatepass = [];
        $errors = [];
        $firstRow = true;
        $lineNumber = 0;

        while (($datalist = fgetcsv($file)) !== FALSE) {
            $lineNumber++;
            if ($firstRow) {
                $firstRow = false; 
                continue;
            }

            if (empty(array_filter($datalist))) {
                continue;
            }

            $gatepass[] = [
                'emp_id' => $datalist[0],
                'date' => $datalist[1],
                'out_time' => $datalist[2],
                'in_time' => $datalist[3],
                'line_number' => $lineNumber,
            ];
        }
        fclose($file);

        $employees = \App\Employee::pluck('emp_id', 'emp_id')->toArray();
        $validRecords = [];
        
        foreach ($gatepass as $gatepassData) {
            $rowValidator = Validator::make($gatepassData, [
                'emp_id' => 'required',
                'date' => 'required',
                'out_time' => 'required',
            ]);

            if ($rowValidator->fails()) {
                $errors[] = 'Line ' . $gatepassData['line_number'] . ': ' . implode(', ', $rowValidator->errors()->all());
                continue;
            }

            if (!isset($employees[$gatepassData['emp_id']])) {
                $errors[] = 'Line ' . $gatepassData['line_number'] . ': Invalid Employee ID ' . $gatepassData['emp_id'];
                continue;
            }

            if(!empty($gatepassData['in_time'])){
                $ontime = Carbon::parse($gatepassData['in_time']); 
                $offtime = Carbon::parse($gatepassData['out_time']); 
                $minutes = $offtime->diffInMinutes($ontime);
            }else{
                $minutes = 0;
            }

            try {
                $date = Carbon::parse($gatepassData['date'])->format('Y-m-d');
                $outTime = Carbon::parse($gatepassData['out_time'])->format('Y-m-d H:i:s');
                $inTime = Carbon::parse($gatepassData['in_time'])->format('Y-m-d H:i:s');

                $validRecords[] = [
                    'emp_id' => $gatepassData['emp_id'],
                    'date' => $date,
                    'intime' => $inTime,
                    'offtime' => $outTime,
                    'minites_count' => $minutes,
                    'status' => '1',
                    'created_by' => Auth::id(),
                    'updated_by' => '0',
                    'approve_status' => '0',
                    'approved_by' => '0',
                ];

            } catch (\Exception $e) {
                $errors[] = 'Line ' . $gatepassData['line_number'] . ': Invalid date or time format - ' . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors]);
        }

        foreach (array_chunk($validRecords, 100) as $chunk) {
            Gatepass::insert($chunk);
        }

        return response()->json([
            'status' => true,
            'msg' => 'Gatepass records uploaded successfully.',
            'count' => count($validRecords),
        ]);
    }
 
}
