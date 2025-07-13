<?php

namespace App\Http\Controllers;

use App\TransportAllocation;
use App\TransportAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use Illuminate\Support\Facades\Input;

class TransportAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
       
        $user = Auth::user();
        $permission = $user->can('transport-allocation-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $employees = DB::table('employees')->select('employees.emp_id','employees.emp_name_with_initial')->get();
        $transportroute = DB::table('transport_routes')->select('transport_routes.id','transport_routes.name')->get();
        $transportvehicle = DB::table('transport_vehicles')->select('transport_vehicles.id','transport_vehicles.vehicle_number')->get();

        return view('Transport.transport_allocation',compact('employees','transportroute','transportvehicle'));
    }

    public function insert(Request $request){
        $user = Auth::user();
        $permission = $user->can('transport-allocation-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
    
        $Transportallocation = new TransportAllocation();
        $Transportallocation->date = $request->input('date');
        $Transportallocation->status = '1';
        $Transportallocation->created_by = Auth::id();
        $Transportallocation->updated_by = '0';
        $Transportallocation->save();
    
        $requestID = $Transportallocation->id;
        $tableData = $request->input('tableData');
    
        foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['employee_id'];
            $route_id = $rowtabledata['route_id'];
            $vehicle_id = $rowtabledata['vehicle_id'];
    
            $Transportallocationdetail = new TransportAllocationdetail();
            $Transportallocationdetail->transport_allocation_id = $requestID;
            $Transportallocationdetail->emp_id = $emp_id;
            $Transportallocationdetail->route_id = $route_id;
            $Transportallocationdetail->vehicle_id = $vehicle_id;
            $Transportallocationdetail->status = '1';
            $Transportallocationdetail->created_by = Auth::id();
            $Transportallocationdetail->updated_by = '0';
            $Transportallocationdetail->save();
        }
        return response()->json(['success' => 'Transport Allocation is Successfully Inserted']);
    }


    public function requestlist()
    {
        $types = DB::table('transport_allocations')
            ->select('transport_allocations.*')
            ->whereIn('transport_allocations.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();


                        $btn .= ' <button name="view" id="'.$row->id.'" class="view btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-eye"></i></button>';

                        if($user->can('transport-allocation-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                        }

                        if($user->can('transport-allocation-status')){
                            if($row->status == 1){
                                $btn .= ' <a href="'.route('TransportAllocationstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            }else{
                                $btn .= '&nbsp;<a href="'.route('TransportAllocationstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            }
                        }
                        if($user->can('transport-allocation-delete')){
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
        $data = DB::table('transport_allocations')
        ->select('transport_allocations.*')
        ->where('transport_allocations.id', $id)
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
        $data = DB::table('transport_allocation_details')
        ->select('transport_allocation_details.*', 'employees.emp_name_with_initial as employee_name','transport_routes.name as route','transport_vehicles.vehicle_number as vehicle')
        ->leftjoin('employees', 'employees.emp_id', 'transport_allocation_details.emp_id')
        ->leftjoin('transport_routes', 'transport_routes.id', 'transport_allocation_details.route_id')
        ->leftjoin('transport_vehicles', 'transport_vehicles.id', 'transport_allocation_details.vehicle_id')
        ->where('transport_allocation_details.transport_allocation_id', $recordID)
        ->where('transport_allocation_details.status', 1)
        ->get(); 

        $htmlTable = '';
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->employee_name . '</td>';
            $htmlTable .= '<td data-id="' . $row->route_id . '">' . $row->route . '</td>'; 
            $htmlTable .= '<td data-id="' . $row->vehicle_id . '">' . $row->vehicle . '</td>';
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
        $data = DB::table('transport_allocation_details')
        ->select('transport_allocation_details.*')
        ->where('transport_allocation_details.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
   }

   public function deletelist(Request $request){

    $user = Auth::user();
    $permission = $user->can('transport-allocation-delete');
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
        TransportAllocationDetail::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Transport Allocation is successfully Deleted']);

    }

    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('transport-allocation-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
       
        $current_date_time = Carbon::now()->toDateTimeString();
        $id = $request->hidden_id;
    
        $form_data = array(
            'date' => $request->date,
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
    
        Transportallocation::findOrFail($id)->update($form_data);
    
        $tableData = $request->input('tableData');
    
        $existingDetailIds = TransportAllocationDetail::where('transport_allocation_id', $id)
            ->where('status', 1) 
            ->pluck('id')->toArray();
        
        $processedDetailIds = [];
    
        foreach ($tableData as $rowtabledata) {
            $emp_id = $rowtabledata['employee_id'];
            $route_id = $rowtabledata['route_id'];
            $vehicle_id = $rowtabledata['vehicle_id'];
    
            $existingAllocation = TransportAllocationDetail::where('transport_allocation_id', $id)
                ->where('emp_id', $emp_id)
                ->where('status', 1)
                ->first();
    
            if(isset($rowtabledata['col_6']) && !empty($rowtabledata['col_6'])) {
                $detailID = $rowtabledata['col_6'];
                $Transportallocationdetail = TransportAllocationDetail::find($detailID);
                if ($Transportallocationdetail) {
                    $Transportallocationdetail->emp_id = $emp_id;
                    $Transportallocationdetail->route_id = $route_id;
                    $Transportallocationdetail->vehicle_id = $vehicle_id;
                    $Transportallocationdetail->updated_by = Auth::id();
                    $Transportallocationdetail->save();
                    
                    $processedDetailIds[] = $detailID;
                }
            } else {
                if (!$existingAllocation) {
                    $Transportallocationdetail = new TransportAllocationDetail();
                    $Transportallocationdetail->emp_id = $emp_id;
                    $Transportallocationdetail->route_id = $route_id;
                    $Transportallocationdetail->vehicle_id = $vehicle_id;
                    $Transportallocationdetail->transport_allocation_id = $id;
                    $Transportallocationdetail->status = '1';
                    $Transportallocationdetail->created_by = Auth::id();
                    $Transportallocationdetail->updated_by = '0';
                    $Transportallocationdetail->save();
                    
                    $processedDetailIds[] = $Transportallocationdetail->id;
                }
            }
        }
        
        return response()->json(['success' => 'Transport Allocation is Successfully Updated']);
    }


    public function view(Request $request){


        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('transport_allocations')
        ->select('transport_allocations.*')
        ->where('transport_allocations.id', $id)
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
        $data = DB::table('transport_allocation_details')
        ->select('transport_allocation_details.*' ,'employees.emp_name_with_initial as employee_name','transport_routes.name as route_name','transport_vehicles.vehicle_number as vehicle_number')
        ->where('transport_allocation_details.transport_allocation_id', $recordID)
        ->leftjoin('employees','employees.emp_id','transport_allocation_details.emp_id')
        ->leftjoin('transport_routes', 'transport_routes.id', 'transport_allocation_details.route_id')
        ->leftjoin('transport_vehicles', 'transport_vehicles.id', 'transport_allocation_details.vehicle_id')
        ->where('transport_allocation_details.status', 1)
        ->get(); 


        $htmlTable = '';
        foreach ($data as $row) {
            
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->emp_id . '</td>'; 
            $htmlTable .= '<td>' . $row->employee_name . '</td>'; 
            $htmlTable .= '<td>' . $row->route_name . '</td>';
            $htmlTable .= '<td>' . $row->vehicle_number . '</td>';
            $htmlTable .= '<td class="d-none">ExistingData</td>'; 
            $htmlTable .= '<td name="detailsId" class="d-none">' . $row->id . '</td>'; 
            $htmlTable .= '</tr>';
        }

        return $htmlTable;

    }

    public function delete(Request $request){

        $user = Auth::user();
        $permission = $user->can('transport-allocation-delete');
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
        Transportallocation::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Transport Allocation is Successfully Deleted']);

    }

    public function status($id,$statusid){
       
        $user = Auth::user();
        $permission = $user->can('transport-allocation-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
                'update_by' => Auth::id(),
            );
            Transportallocation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('TransportAllocation');
        } else{
            $form_data = array(
                'status' =>  '2',
                'update_by' => Auth::id(),
            );
            Transportallocation::findOrFail($id)
            ->update($form_data);
    
            return redirect()->route('TransportAllocation');
        }

    }

    public function employee_list_for_transport(Request $request)
    {
        if ($request->ajax())
        {
            $searchTerm = $request->get('term');
            $date = $request->get('date');
            
            $allocatedEmpIds = [];
            if ($date) {
                $allocatedEmpIds = DB::table('ot_allocation')
                    ->join('ot_allocationdetails', 'ot_allocation.id', '=', 'ot_allocationdetails.ot_allocation_id')
                    ->where('ot_allocation.date', $date)
                    ->where('ot_allocation.transport', 1)
                    ->pluck('ot_allocationdetails.emp_id')
                    ->toArray();
            }

            $employees = DB::table('employees')
                ->leftJoin('transport_routes', 'transport_routes.id', '=', 'employees.route_id')
                ->select(
                    'employees.emp_id as id',
                    DB::raw('CONCAT(emp_name_with_initial, " - ", employees.emp_id, " - ", IFNULL(transport_routes.name, "No Route")) as text')
                )
                ->where('is_resigned', 0)
                ->where('deleted', 0)
                ->when(!empty($date), function($query) use ($allocatedEmpIds) {
                    $query->whereIn('emp_id', $allocatedEmpIds);
                })
                ->when(!empty($searchTerm), function ($query) use ($searchTerm) {
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('employees.emp_id', 'like', "%{$searchTerm}%")
                        ->orWhere('employees.emp_name_with_initial', 'like', "%{$searchTerm}%")
                        ->orWhere('transport_routes.name', 'like', "%{$searchTerm}%");
                    });
                }, function ($query) {
                    $query->limit(5);
                })
                ->orderBy('employees.emp_id', 'asc')
                ->get();

            return response()->json(['results' => $employees]);
        }
    }


}
