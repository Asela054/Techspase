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
use Illuminate\Support\Facades\Input;

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

        $transportroute = DB::table('transport_routes')->select('transport_routes.id','transport_routes.name')->get();
        $transportvehicle = DB::table('transport_vehicles')->select('transport_vehicles.id','transport_vehicles.vehicle_number')->get();
        $employees = DB::table('employees')->select('employees.emp_id','employees.emp_name_with_initial')->get();

        return view('EmployeeShift.ot_allocation',compact('employees', 'transportroute', 'transportvehicle'));
    }

    public function insert(Request $request) {
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
        $tableData = $request->input('tableData');

        foreach ($tableData as $rowtabledata) {
            $Otallocationdetail = new Otallocationdetail();
            $Otallocationdetail->ot_allocation_id = $requestID;
            $Otallocationdetail->emp_id = $rowtabledata['col_1']; 
            $Otallocationdetail->route_id = $rowtabledata['col_8'] ?? 0; 
            $Otallocationdetail->vehicle_id = $rowtabledata['col_9'] ?? 0; 
            $Otallocationdetail->time_from = $request->input('time_from');
            $Otallocationdetail->time_to = $request->input('time_to');
            $Otallocationdetail->transport = $rowtabledata['col_6'] ?? 0; 
            $Otallocationdetail->meal = $rowtabledata['col_7'] ?? 0; 
            $Otallocationdetail->status = '1';
            $Otallocationdetail->created_by = Auth::id();
            $Otallocationdetail->updated_by = '0';
            $Otallocationdetail->save();
        }

        return response()->json(['success' => 'Employee OT Allocation successfully inserted']);
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
            ->leftjoin('transport_vehicles', 'transport_vehicles.id', 'ot_allocationdetails.vehicle_id')
            ->leftjoin('transport_routes', 'transport_routes.id', 'ot_allocationdetails.route_id')
            ->where('ot_allocationdetails.ot_allocation_id', $recordID)
            ->where('ot_allocationdetails.status', 1)
            ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->employee_name . '</td>';
            $htmlTable .= '<td>' . date('H:i', strtotime($row->time_from)) . ' - ' . date('H:i', strtotime($row->time_to)) . '</td>'; 
            // Hidden columns
            $htmlTable .= '<td class="d-none">' . date('H:i', strtotime($row->time_from)) . '</td>';
            $htmlTable .= '<td class="d-none">' . date('H:i', strtotime($row->time_to)) . '</td>';
            $htmlTable .= '<td class="d-none">' . $row->transport . '</td>';
            $htmlTable .= '<td class="d-none">' . $row->meal . '</td>';
            $htmlTable .= '<td class="d-none">' . $row->route_id . '</td>';
            $htmlTable .= '<td class="d-none">' . $row->vehicle_id . '</td>';
            $htmlTable .= '<td class="d-none">ExistingData</td>'; 
            $htmlTable .= '<td class="d-none">' . $row->id . '</td>'; 
            $htmlTable .= '<td class="text-right" id="actionrow">
                <button type="button" rowid="'.$row->id.'" id="btnDeleterow" class="btnDeletelist btn btn-danger btn-sm">
                <i class="fas fa-trash-alt"></i>
                </button></td>'; 
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
                ->first(); 
                
            return response()->json([
                'result' => [
                    'id' => $data->id,
                    'emp_id' => $data->emp_id,
                    'time_from' => date('H:i', strtotime($data->time_from)),
                    'time_to' => date('H:i', strtotime($data->time_to)),
                    'transport' => $data->transport,
                    'meal' => $data->meal,
                    'route_id' => $data->route_id,
                    'vehicle_id' => $data->vehicle_id
                ]
            ]);
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


    public function update(Request $request) {
        $user = Auth::user();
        $permission = $user->can('employee-ot-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $current_date_time = Carbon::now()->toDateTimeString();
        $id = $request->hidden_id;

        $mainAllocation = Otallocation::findOrFail($id);
        
        $form_data = array(
            'date' => $request->date,
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );

        $mainAllocation->update($form_data);

        $tableData = $request->input('tableData');
        $existingDetailIds = Otallocationdetail::where('ot_allocation_id', $id)
            ->pluck('id')->toArray();
        $processedDetailIds = [];

        foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['col_1'];
            
            $time_from = $mainAllocation->time_from;
            $time_to = $mainAllocation->time_to;

            $transport = $rowtabledata['col_6'] ?? $mainAllocation->transport;
            $meal = $rowtabledata['col_7'] ?? $mainAllocation->meal;
            $route_id = $rowtabledata['col_8'] ?? 0;
            $vehicle_id = $rowtabledata['col_9'] ?? 0;

            if(isset($rowtabledata['col_11']) && !empty($rowtabledata['col_11'])) {
                // Update existing record
                $detailID = $rowtabledata['col_11'];
                $form_detail = array(
                    'emp_id' => $emp_id,
                    'time_from' => $time_from,
                    'time_to' => $time_to,
                    'transport' => $transport,
                    'meal' => $meal,
                    'route_id' => $route_id,
                    'vehicle_id' => $vehicle_id,
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time,
                );
                Otallocationdetail::findOrFail($detailID)->update($form_detail);
                $processedDetailIds[] = $detailID;
            } else {
                // Create new record
                $Otallocationdetail = new Otallocationdetail();
                $Otallocationdetail->ot_allocation_id = $id;
                $Otallocationdetail->emp_id = $emp_id;
                $Otallocationdetail->time_from = $time_from;
                $Otallocationdetail->time_to = $time_to;
                $Otallocationdetail->transport = $transport;
                $Otallocationdetail->meal = $meal;
                $Otallocationdetail->route_id = $route_id;
                $Otallocationdetail->vehicle_id = $vehicle_id;
                $Otallocationdetail->status = '1';
                $Otallocationdetail->created_by = Auth::id();
                $Otallocationdetail->save();
                $processedDetailIds[] = $Otallocationdetail->id;
            }
        }

        // Delete any records that weren't processed
        $toDelete = array_diff($existingDetailIds, $processedDetailIds);
        if (!empty($toDelete)) {
            Otallocationdetail::whereIn('id', $toDelete)
                ->update([
                    'status' => '3',
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time
                ]);
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
    private function view_reqestcountlist($id) {
        $recordID = $id;
        $data = DB::table('ot_allocationdetails')
            ->select(
                'ot_allocationdetails.*',
                'employees.emp_name_with_initial as employee_name',
                'transport_routes.name as route_name',
                'transport_vehicles.vehicle_number'
            )
            ->where('ot_allocationdetails.ot_allocation_id', $recordID)
            ->leftjoin('employees', 'employees.emp_id', 'ot_allocationdetails.emp_id')
            ->leftjoin('transport_vehicles', 'transport_vehicles.id', 'ot_allocationdetails.vehicle_id')
            ->leftjoin('transport_routes', 'transport_routes.id', 'ot_allocationdetails.route_id')
            ->where('ot_allocationdetails.status', 1)
            ->get(); 

        $htmlTable = '';
        $counter = 1;
        
        foreach ($data as $row) {
            $timeFrom = date('H:i', strtotime($row->time_from));
            $timeTo = date('H:i', strtotime($row->time_to));
            
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $counter++ . '</td>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->employee_name . '</td>';
            $htmlTable .= '<td>' . ($row->route_name ?? 'N/A') . '</td>';
            $htmlTable .= '<td>' . ($row->vehicle_number ?? 'N/A') . '</td>';
            $htmlTable .= '<td>' . $timeFrom . ' - ' . $timeTo . '</td>';
            $htmlTable .= '<td>';
            $htmlTable .= $row->transport ? '<span class="badge badge-info mr-1">Transport</span>' : '';
            $htmlTable .= $row->meal ? '<span class="badge badge-warning text-dark">Meal</span>' : '';
            $htmlTable .= '</td>';
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
        $successCount = 0;
        $lineNumber = 2; 

        foreach ($fileContents as $line) {
            if (trim($line) === '') {
                $lineNumber++;
                continue;
            }

            $data = str_getcsv($line);

            if (count($data) < 4) {
                $errors[] = "Line {$lineNumber}: Invalid format - must contain employee ID, date, start time, and end time";
                $lineNumber++;
                continue;
            }

            $emp_id = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[0]));
            $date_raw = trim(str_replace(["\r", "\n", " "], '', $data[1]));
            $time_from = trim($data[2]);  
            $time_to = trim($data[3]);
            $transport = isset($data[4]) ? trim($data[4]) : '0';
            $meal = isset($data[5]) ? trim($data[5]) : '0';    

            if (empty($emp_id)) {
                $errors[] = "Line {$lineNumber}: Missing employee ID";
                $lineNumber++;
                continue;
            }

            $date = date('Y-m-d', strtotime($date_raw));
            if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
                $errors[] = "Line {$lineNumber}: Invalid date format '{$date_raw}' - must be YYYY-MM-DD";
                $lineNumber++;
                continue;
            }

            $emp = DB::table('employees')->where('emp_id', $emp_id)->first();
            if (is_null($emp)) {
                $errors[] = "Line {$lineNumber}: Employee ID {$emp_id} not found in the database";
                $lineNumber++;
                continue;
            }

            $datetime_from = $date . ' ' . $time_from;
            $datetime_to = $date . ' ' . $time_to;

            if (strtotime($datetime_to) <= strtotime($datetime_from)) {
                $errors[] = "Line {$lineNumber}: End time must be after start time for employee {$emp_id}";
                $lineNumber++;
                continue;
            }

            $route_id = null;
            if ($transport == '1' && !empty($emp->route_id)) {
                $route_id = $emp->route_id;
            }

            try {
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

                Otallocationdetail::firstOrCreate(
                    [
                        'ot_allocation_id' => $Otallocation->id,
                        'emp_id' => $emp_id,
                    ],
                    [
                        'date' => $date,
                        'time_from' => $datetime_from,
                        'time_to' => $datetime_to,
                        'transport' => $transport,
                        'meal' => $meal,
                        'route_id' => $route_id ?? 0,
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

    public function employee_list_for_ot(Request $request)
    {
        if ($request->ajax())
        {
            $searchTerm = Input::get('term');
            $employees = DB::table('employees')
                ->select(DB::raw('emp_id as id, CONCAT(emp_name_with_initial, " - ", employees.emp_id) as text, route_id'))
                ->where('is_resigned', 0)
                ->where('deleted', 0)
                ->where('status', 1)
                ->when(!empty($searchTerm), function ($query) use ($searchTerm) {
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('emp_id', 'like', "%{$searchTerm}%")
                        ->orWhere('emp_name_with_initial', 'like', "%{$searchTerm}%");
                    });
                }, function ($query) {
                    $query->limit(5);
                })
                ->orderBy('emp_id', 'asc')
                ->get();

            return response()->json(['results' => $employees]);
        }
    }

}
