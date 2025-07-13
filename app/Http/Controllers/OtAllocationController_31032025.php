<?php

namespace App\Http\Controllers;

use App\Otallocation;
use App\Otallocationdetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class OtAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
       
        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employees = DB::table('employees')->select('employees.emp_id','employees.emp_name_with_initial')->get();

        return view('EmployeeShift.ot_allocation',compact('employees'));
    }

    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $Otallocation = new Otallocation();
        $Otallocation->date = $request->input('date');
        $Otallocation->time_from = $request->input('time_from');  
        $Otallocation->time_to = $request->input('time_to');      
        $Otallocation->status = '1';
        $Otallocation->created_by = Auth::id();
        $Otallocation->updated_by = '0';
        $Otallocation->save();
    
        $requestID = $Otallocation->id;
        $time_from = $request->input('time_from');  
        $time_to = $request->input('time_to');     
        $tableData = $request->input('tableData');
    
        foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['col_1'];
    
            $Otallocationdetail = new Otallocationdetail();
            $Otallocationdetail->ot_allocation_id = $requestID;
            $Otallocationdetail->emp_id = $emp_id;
            $Otallocationdetail->time_from = $time_from;
            $Otallocationdetail->time_to = $time_to;
            $Otallocationdetail->status = '1';
            $Otallocationdetail->created_by = Auth::id();
            $Otallocationdetail->updated_by = '0';
            $Otallocationdetail->save();
        }
        return response()->json(['success' => 'Employee Ot Allocation is Successfully Inserted']);
    }


    public function requestlist()
    {
        $types = DB::table('ot_allocation')
            ->select('ot_allocation.*')
            ->whereIn('ot_allocation.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();


                        $btn .= ' <button name="view" id="'.$row->id.'" class="view btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-eye"></i></button>';

                        if($user->can('employee-ot-allocation-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                        }

                        if($user->can('employee-ot-allocation-status')){
                            if($row->status == 1){
                                $btn .= ' <a href="'.route('employeeotstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            }else{
                                $btn .= '&nbsp;<a href="'.route('employeeotstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            }
                        }
                        if($user->can('employee-ot-allocation-delete')){
                            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                        }
              
                return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit(Request $request){


        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('ot_allocation')
        ->select('ot_allocation.*')
        ->where('ot_allocation.id', $id)
        ->get(); 
        $requestlist = $this->reqestcountlist($id); 
    
        $responseData = array(
            'mainData' => $data[0],
            'requestdata' => $requestlist,
        );

    return response() ->json(['result'=>  $responseData]);
    }
    }

    private function reqestcountlist($id){
        $recordID = $id;
        $data = DB::table('ot_allocationdetails')
        ->select('ot_allocationdetails.*', 'employees.emp_name_with_initial as employee_name')
        ->leftjoin('employees', 'employees.emp_id', 'ot_allocationdetails.emp_id')
        ->where('ot_allocationdetails.ot_allocation_id', $recordID)
        ->where('ot_allocationdetails.status', 1)
        ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->employee_name . '</td>';
            $htmlTable .= '<td>' . date('H:i', strtotime($row->time_from)) . ' - ' . date('H:i', strtotime($row->time_to)) . '</td>'; 
            $htmlTable .= '<td class="d-none">ExistingData</td>'; 
            $htmlTable .= '<td name="detailsId" class="d-none">' . $row->id . '</td>'; 
            $htmlTable .= '<td class="text-right" id="actionrow"><button type="button" id="'.$row->id.'" class="btnEditlist btn btn-primary btn-sm">
                <i class="fas fa-pen"></i>
                </button>&nbsp;
                <button type="button" rowid="'.$row->id.'" id="btnDeleterow" class="btnDeletelist btn btn-danger btn-sm">
                <i class="fas fa-trash-alt"></i>
                </button></td>'; 
            $htmlTable .= '<td class="d-none"><input type="hidden" id="hiddenid" name="hiddenid" value="'.$row->id.'"></td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;
    }
   public function editlist(Request $request){
        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('ot_allocationdetails')
        ->select('ot_allocationdetails.*')
        ->where('ot_allocationdetails.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
   }

   public function deletelist(Request $request){

    $user = Auth::user();
    $permission = $user->can('employee-ot-allocation-delete');
    if (!$permission) {
        return response()->json(['error' => 'UnAuthorized'], 401);
    }

        $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'update_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Otallocationdetail::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Employee OT Allocation is successfully Deleted']);

    }


    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
       
        $current_date_time = Carbon::now()->toDateTimeString();
        $id = $request->hidden_id;
    
        // Update main allocation record
        $form_data = array(
            'date' => $request->date,
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
    
        Otallocation::findOrFail($id)->update($form_data);
    
        $tableData = $request->input('tableData');
    
        foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['col_1'];
            $dataType = $rowtabledata['col_3']; 
            
            if($dataType == "ExistingData") {
                continue;
            }
            else if($dataType == "Updated") {
                $detailID = $rowtabledata['col_4'];
                $Otallocationdetail = Otallocationdetail::where('id', $detailID)->first();
                $Otallocationdetail->time_from = $request->time_from;
                $Otallocationdetail->time_to = $request->time_to;
                $Otallocationdetail->emp_id = $emp_id;
                $Otallocationdetail->updated_by = Auth::id();
                $Otallocationdetail->save();
            }
            else if($dataType == "NewData") {
                $Otallocationdetail = new Otallocationdetail();
                $Otallocationdetail->ot_allocation_id = $id;
                $Otallocationdetail->emp_id = $emp_id;
                $Otallocationdetail->time_from = $request->time_from;
                $Otallocationdetail->time_to = $request->time_to;
                $Otallocationdetail->status = '1';
                $Otallocationdetail->created_by = Auth::id();
                $Otallocationdetail->updated_by = '0';
                $Otallocationdetail->save();
            }
        }
        
        return response()->json(['success' => 'Employee OT allocation is Successfully Updated']);
    }


    public function view(Request $request){


        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('ot_allocation')
        ->select('ot_allocation.*')
        ->where('ot_allocation.id', $id)
        ->get(); 
        $requestlist = $this->view_reqestcountlist($id); 

        $responseData = array(
            'mainData' => $data[0],
            'requestdata' => $requestlist,
        );

        return response() ->json(['result'=>  $responseData]);
        }
    }
    private function view_reqestcountlist($id){

        $recordID =$id ;
        $data = DB::table('ot_allocationdetails')
        ->select('ot_allocationdetails.*' ,'employees.emp_name_with_initial as employee_name')
        ->where('ot_allocationdetails.ot_allocation_id', $recordID)
        ->leftjoin('employees','employees.emp_id','ot_allocationdetails.emp_id')
        ->where('ot_allocationdetails.status', 1)
        ->get(); 


        $htmlTable = '';
        foreach ($data as $row) {
            
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->employee_name . '</td>'; 
            $htmlTable .= '<td class="d-none">ExistingData</td>'; 
            $htmlTable .= '<td name="detailsId" class="d-none">' . $row->id . '</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;

    }

    public function delete(Request $request){

        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
            $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'update_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Otallocation::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Employee Ot allocation is Successfully Deleted']);

    }

    public function status($id,$statusid){
       
        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
                'update_by' => Auth::id(),
            );
            Otallocation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('employeeot');
        } else{
            $form_data = array(
                'status' =>  '2',
                'update_by' => Auth::id(),
            );
            Otallocation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('employeeot');
        }

    }


    public function ot_allocate_csv(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $file = $request->file('csv_file_u');
        $fileContents = file($file->getPathname());
        
        array_shift($fileContents);
        
        $errors = [];

        foreach ($fileContents as $line) {
            $data = str_getcsv($line);

            if (count($data) < 4) {
                $errors[] = "Invalid row format: " . implode(',', $data);
                continue;
            }

            $emp_id = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[0]));
            $date_raw = trim(str_replace(["\r", "\n", " "], '', $data[1]));
            $time_from = trim($data[2]);  
            $time_to = trim($data[3]);    

            $date = date('Y-m-d', strtotime($date_raw));
            if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
                $errors[] = "Invalid date format for employee {$emp_id}: {$date_raw}";
                continue;
            }

            $datetime_from = $date . ' ' . $time_from;
            $datetime_to = $date . ' ' . $time_to;

            $emp = DB::table('employees')->where('emp_id', $emp_id)->first();
            if (is_null($emp)) {
                $errors[] = "Employee ID {$emp_id} not found in the database.";
                continue;
            }

            $Otallocation = Otallocation::firstOrCreate(
                [
                    'date' => $date,
                    'time_from' => $datetime_from,
                    'time_to' => $datetime_to,
                ],
                [
                    'status' => '1',
                    'created_by' => Auth::id(),
                    'updated_by' => '0',
                ]
            );

            $requestID = $Otallocation->id;

            Otallocationdetail::firstOrCreate(
                [
                    'ot_allocation_id' => $requestID,
                    'emp_id' => $emp_id,
                ],
                [
                    'date' => $date,
                    'time_from' => $datetime_from,
                    'time_to' => $datetime_to,
                    'status' => '1',
                    'created_by' => Auth::id(),
                    'updated_by' => '0',
                ]
            );
        }

        if (!empty($errors)) {
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }

        return response()->json(['status' => true, 'msg' => 'CSV uploaded successfully.']);
    }



}
