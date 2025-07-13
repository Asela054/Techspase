<?php

namespace App\Http\Controllers;

use App\EmployeeGrade;
use http\Env\Response;
use Illuminate\Http\Request;
use Validator;

class EmployeeGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('emp-grade-list');
        if(!$permission){
            abort(403);
        }

        $empgrade= EmployeeGrade::orderBy('id', 'asc')->get();
        return view('Employeermasterfiles.empGrade',compact('empgrade'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('emp-grade-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        $rules = array(
            'grade'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'grade' =>  $request->grade
        );

        $empgrade=new EmployeeGrade;
        $empgrade->grade=$request->input('grade'); 
        $empgrade->save();

        return response()->json(['success' => 'Data Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeGrade $payGrade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('emp-grade-edit');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        if(request()->ajax())
        {
            $data = EmployeeGrade::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeGrade $empGrade)
    {
        $user = auth()->user();
        $permission = $user->can('emp-grade-edit');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        $rules = array(
            'grade'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'grade' =>  $request->grade
            
        );

        EmployeeGrade::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Grade is Successfully Updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('emp-grade-delete');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        $data = EmployeeGrade::findOrFail($id);
        $data->delete();
    }
}
