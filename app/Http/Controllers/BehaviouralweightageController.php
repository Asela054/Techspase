<?php

namespace App\Http\Controllers;

use App\Behaviouralweightage;
use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class BehaviouralweightageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        
        $behaviouraltypes = DB::table('behaviouraltypes')->select('behaviouraltypes.*')
        ->whereIn('behaviouraltypes.status', [1, 2])
        ->where('behaviouraltypes.status', 1)
        ->get();

        $employees = DB::table('employees')
        ->select('employees.*')
        ->get();

        return view('KPImanagement.behaviouralweightage',compact('behaviouraltypes','employees'));
    }
    public function insert(Request $request){

        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $tableData = $request->input('tableData');

    foreach ($tableData as $rowtabledata) {
        $type = $rowtabledata['col_1'];
        $weightage = $rowtabledata['col_2'];

        $behaviouralweightage = new Behaviouralweightage();
        $behaviouralweightage->emp_id = $request->input('emp');  
        $behaviouralweightage->type_id = $type;
        $behaviouralweightage->weightage = $weightage;
        $behaviouralweightage->status = '1';
        $behaviouralweightage->created_by = Auth::id();
        $behaviouralweightage->updated_by = '0';
        $behaviouralweightage->save();

    }
        return response()->json(['success' => 'Behavioural Weightage is Successfully Inserted']);
    }

    public function requestlist()
    {
        $types = DB::table('behaviouralweightages')
            ->leftjoin('behaviouraltypes', 'behaviouralweightages.type_id', '=', 'behaviouraltypes.id')
            ->leftjoin('employees', 'behaviouralweightages.emp_id', '=', 'employees.emp_id')
            ->select('behaviouralweightages.*','behaviouraltypes.type AS type','employees.emp_name_with_initial AS empname')
            ->whereIn('behaviouralweightages.status', [1, 2])
            ->get();

            return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 


                            // if($row->status == 1){
                            //     $btn .= ' <a href="'.route('behaviouralweightagestatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                            // }else{
                            //     $btn .= '&nbsp;<a href="'.route('behaviouralweightagestatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                            // }

                            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
              
                return $btn;
            })
           
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit(Request $request){
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('behaviouralweightages')
        ->select('behaviouralweightages.*')
        ->where('behaviouralweightages.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
    }
    }




    public function update(Request $request){
        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
       
            $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id ;
        $form_data = array(
                'emp_id' => $request->emp,
                'type_id' => $request->type,
                'weightage' => $request->weightage,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

            Behaviouralweightage::findOrFail($id)
        ->update($form_data);
        
        return response()->json(['success' => 'Behavioural Weightage is Successfully Updated']);
    }




    public function delete(Request $request){

        $user = Auth::user();
        $permission = $user->can('Behavioural-status');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
        $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Behaviouralweightage::findOrFail($id)
        ->update($form_data);

        return response()->json(['success' => 'Behavioural Weightage is Successfully Deleted']);

    }

    // public function status($id,$statusid){

    //     $user = Auth::user();
    //     $permission = $user->can('Behavioural-status');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     } 

    //     if($statusid == 1){
    //         $form_data = array(
    //             'status' =>  '1',
    //             'updated_by' => Auth::id(),
    //         );
    //         Behaviouralweightage::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('behaviouralweightage');
    //     } else{
    //         $form_data = array(
    //             'status' =>  '2',
    //             'updated_by' => Auth::id(),
    //         );
    //         Behaviouralweightage::findOrFail($id)
    //         ->update($form_data);
    
    //         return redirect()->route('behaviouralweightage');
    //     }

    // }

}
