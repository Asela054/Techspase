<?php

namespace App\Http\Controllers;

use App\Employeeshift;
use App\Employeeshiftdetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class EmployeeShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
       
        $user = Auth::user();
        $permission = $user->can('employee-shift-allocation-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        // $employees = DB::table('employees')->select('employees.*')->get();
        $shifttypes = DB::table('shift_types')->select('shift_types.*')->get();

        return view('EmployeeShift.employeeshift',compact('shifttypes','employees'));
    }

    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('employee-shift-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $shift_id = $request->input('shift');
        $dates = $request->input('dates');  
        $tableData = $request->input('tableData');
    
        foreach ($dates as $date) {
            $existingShift = Employeeshift::where('shift_id', $shift_id)
                ->where('date_from', $date)
                ->first();
    
            if (!$existingShift) {
                $Employeeshift = new Employeeshift();
                $Employeeshift->shift_id = $shift_id;
                $Employeeshift->date_from = $date;
                $Employeeshift->date_to = $date;  
                $Employeeshift->status = '1';
                $Employeeshift->created_by = Auth::id();
                $Employeeshift->updated_by = '0';
                $Employeeshift->save();
    
                $requestID = $Employeeshift->id;
            } else {
                $requestID = $existingShift->id;
            }
    
            foreach ($tableData as $rowtabledata) {
                $emp_id = $rowtabledata['col_1'];
                $empname = $rowtabledata['col_2'];
    
                $existingDetail = Employeeshiftdetail::where('employeeshift_id', $requestID)
                    ->where('emp_id', $emp_id)
                    ->where('date_from', $date)
                    ->first();
    
                if (!$existingDetail) {
                    $Employeeshiftdetail = new Employeeshiftdetail();
                    $Employeeshiftdetail->shift_id = $shift_id;
                    $Employeeshiftdetail->date_from = $date;
                    $Employeeshiftdetail->date_to = $date;  
                    $Employeeshiftdetail->emp_id = $emp_id;
                    $Employeeshiftdetail->employee_name = $empname;
                    $Employeeshiftdetail->employeeshift_id = $requestID;
                    $Employeeshiftdetail->status = '1';
                    $Employeeshiftdetail->created_by = Auth::id();
                    $Employeeshiftdetail->updated_by = '0';
                    $Employeeshiftdetail->save();
                }
            }
        }
    
        return response()->json(['success' => 'Employee Shift is Successfully Inserted']);
    }


    public function requestlist()
    {
        $types = DB::table('employeeshifts')
            // ->leftJoin('shift_types','shift_types.id','employeeshifts.shift_id')
            ->select('employeeshifts.*')
            ->whereIn('employeeshifts.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();


                        $btn .= ' <button name="view" id="'.$row->id.'" class="view btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-eye"></i></button>';

                        if($user->can('employee-shift-allocation-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                        }

                        if($user->can('employee-shift-allocation-status')){
                            if($row->status == 1){
                                $btn .= ' <a href="'.route('employeeshiftstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            }else{
                                $btn .= '&nbsp;<a href="'.route('employeeshiftstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            }
                        }
                        if($user->can('employee-shift-allocation-delete')){
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
        $data = DB::table('employeeshifts')
        ->select('employeeshifts.*')
        ->where('employeeshifts.id', $id)
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

        $recordID =$id ;
       $data = DB::table('employeeshiftdetails')
       ->select('employeeshiftdetails.*')
       ->where('employeeshiftdetails.employeeshift_id', $recordID)
       ->where('employeeshiftdetails.status', 1)
       ->get(); 


       $htmlTable = '';
       foreach ($data as $row) {
          
        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
        $htmlTable .= '<td>' . $row->employee_name . '</td>'; 
        $htmlTable .= '<td>' . $row->date_from . '</td>'; 
        $htmlTable .= '<td class="d-none">ExistingData</td>'; 
        $htmlTable .= '<td class="d-none">' . $row->id . '</td>'; 
        $htmlTable .= '<td class="text-right" id ="actionrow"><button type="button" id="'.$row->id.'" class="btnEditlist btn btn-primary btn-sm ">
            <i class="fas fa-pen"></i>
            </button>&nbsp;
            <button type="button" rowid="'.$row->id.'" id="btnDeleterow"  class="btnDeletelist btn btn-danger btn-sm " >
            <i class="fas fa-trash-alt"></i>
            </button></td>'; 
        $htmlTable .= '</tr>';
       }

       return $htmlTable;

   }
   public function editlist(Request $request){
        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('employeeshiftdetails')
        ->select('employeeshiftdetails.*')
        ->where('employeeshiftdetails.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
   }

   public function deletelist(Request $request){

    $user = Auth::user();
    $permission = $user->can('employee-shift-allocation-delete');
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
        Employeeshiftdetail::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Employee Shift is successfully Deleted']);

    }

    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('employee-shift-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
       
        $current_date_time = Carbon::now()->toDateTimeString();
    
        $id = $request->hidden_id;
        $date_from = $request->dates[0]; 
        $date_to = $request->dates[0];   
    
        $form_data = array(
            'shift_id' => $request->shift,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Employeeshift::findOrFail($id)->update($form_data);
    
        $tableData = $request->input('tableData');
        
        $existingDetailIds = Employeeshiftdetail::where('employeeshift_id', $id)
            ->pluck('id')->toArray();
        
        $processedDetailIds = [];
    
        foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['col_1'];
            $empname = $rowtabledata['col_2'];
            
            if(isset($rowtabledata['col_4']) && !empty($rowtabledata['col_4'])) {
                $detailID = $rowtabledata['col_4'];
                
                $Employeeshiftdetail = Employeeshiftdetail::find($detailID);
                if ($Employeeshiftdetail) {
                    $Employeeshiftdetail->shift_id = $request->shift;
                    $Employeeshiftdetail->date_from = $date_from;
                    $Employeeshiftdetail->date_to = $date_to;
                    $Employeeshiftdetail->emp_id = $emp_id;
                    $Employeeshiftdetail->employee_name = $empname;
                    $Employeeshiftdetail->updated_by = Auth::id();
                    $Employeeshiftdetail->save();
                    
                    $processedDetailIds[] = $detailID;
                }
            } else {
                $Employeeshiftdetail = new Employeeshiftdetail();
                $Employeeshiftdetail->shift_id = $request->shift;
                $Employeeshiftdetail->date_from = $date_from;
                $Employeeshiftdetail->date_to = $date_to;
                $Employeeshiftdetail->emp_id = $emp_id;
                $Employeeshiftdetail->employee_name = $empname;
                $Employeeshiftdetail->employeeshift_id = $id;
                $Employeeshiftdetail->status = '1';
                $Employeeshiftdetail->created_by = Auth::id();
                $Employeeshiftdetail->updated_by = '0';
                $Employeeshiftdetail->save();
                
                $processedDetailIds[] = $Employeeshiftdetail->id;
            }
        }
        
        return response()->json(['success' => 'Employee Shift is Successfully Updated']);
    }
    


    public function view(Request $request){


        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('employeeshifts')
        ->select('employeeshifts.*')
        ->where('employeeshifts.id', $id)
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
        $data = DB::table('employeeshiftdetails')
        ->select('employeeshiftdetails.*')
        ->where('employeeshiftdetails.employeeshift_id', $recordID)
        ->where('employeeshiftdetails.status', 1)
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
        $permission = $user->can('employee-shift-allocation-delete');
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
        Employeeshift::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Employee Shift is Successfully Deleted']);

    }

    public function status($id,$statusid){
       
        $user = Auth::user();
        $permission = $user->can('employee-shift-allocation-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
                'update_by' => Auth::id(),
            );
            Employeeshift::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('employeeshift');
        } else{
            $form_data = array(
                'status' =>  '2',
                'update_by' => Auth::id(),
            );
            Employeeshift::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('employeeshift');
        }

    }

    public function night_shiftallocate_csv(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('employee-shift-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $shiftType = $request->input('csv_shift');
        $file = $request->file('csv_file_u');

        $fileContents = file($file->getPathname());
        array_shift($fileContents); 
        
        $errors = [];
        $successCount = 0;
        $lineNumber = 2; 

        foreach ($fileContents as $line) {
            if (trim($line) === '') {
                $lineNumber++;
                continue;
            }

            $data = str_getcsv($line);

            if (count($data) < 2) {
                $errors[] = "Line {$lineNumber}: Invalid format - must contain employee ID and date";
                $lineNumber++;
                continue;
            }

            $emp_id = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[0]));
            $date_raw = trim(str_replace(["\r", "\n", " "], '', $data[1]));
            $date = date('Y-m-d', strtotime($date_raw));

            if (empty($emp_id)) {
                $errors[] = "Line {$lineNumber}: Missing employee ID";
                $lineNumber++;
                continue;
            }

            if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
                $errors[] = "Line {$lineNumber}: Invalid date format '{$date_raw}' - must be YYYY-MM-DD";
                $lineNumber++;
                continue;
            }

            $emp = DB::table('employees')
                ->select('emp_id', 'emp_name_with_initial')
                ->where('emp_id', $emp_id)
                ->first();

            if (is_null($emp)) {
                $errors[] = "Line {$lineNumber}: Employee ID {$emp_id} not found in the database";
                $lineNumber++;
                continue;
            }

            try {
                $Employeeshift = Employeeshift::firstOrCreate(
                    [
                        'shift_id' => $shiftType,
                        'date_from' => $date,
                        'date_to' => $date,
                    ],
                    [
                        'status' => '1',
                        'created_by' => Auth::id(),
                        'updated_by' => '0',
                    ]
                );

                Employeeshiftdetail::firstOrCreate(
                    [
                        'shift_id' => $shiftType,
                        'date_from' => $date,
                        'date_to' => $date,
                        'emp_id' => $emp_id,
                    ],
                    [
                        'employee_name' => $emp->emp_name_with_initial,
                        'employeeshift_id' => $Employeeshift->id,
                        'status' => '1',
                        'created_by' => Auth::id(),
                        'updated_by' => '0',
                    ]
                );

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Line {$lineNumber}: Error processing record - " . $e->getMessage();
            }

            $lineNumber++;
        }

        $response = [
            'status' => $successCount > 0,
            'msg' => "Successfully processed {$successCount} records"
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response);
    }


}
