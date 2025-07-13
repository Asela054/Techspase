<?php

namespace App\Http\Controllers;

use App\Department;
use App\Departmentline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class DepartmentLineController extends Controller
{
    public function index(){

        $department= Department::orderBy('id', 'asc')->get();
        return view('Employeermasterfiles.departmentlines',compact('department'));
     }

     public function insert(Request $request)
     {
        $lines = new Departmentline();
        $lines->department_id = $request->input('department');
        $lines->line = $request->input('line');
        $lines->status = '1';
        $lines->save();
        return response()->json(['success' => 'Department Line is successfully Inserted']);
    }

      public function edit(Request $request)
      {
       
        $id = Request('id');
        if (request()->ajax()){

        $data = DB::table('department_lines')
        ->select('department_lines.*')
        ->where('department_lines.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
         }

      }

       public function update(Request $request)
       {

        $current_date_time = Carbon::now()->toDateTimeString();

        $id =  $request->hidden_id;
        $form_data = array(
                'department_id' => $request->department,
                'line' => $request->line,
                'updated_at' => $current_date_time,
            );

            Departmentline::findOrFail($id)->update($form_data);
        
           return response()->json(['success' => 'Department Line is Successfully Updated']);
       }


       public function delete(Request $request)
       {

        $id = Request('id');
            $current_date_time = Carbon::now()->toDateTimeString();
            $form_data = array(
                'status' =>  '3',
                'updated_at' => $current_date_time,
            );
            Departmentline::findOrFail($id)
            ->update($form_data);

            return response()->json(['success' => 'Department Line is successfully Deleted']);

       }


        public function status($id,$statusid)
        {
            if($statusid == 1){
                $form_data = array(
                    'status' =>  '1',
                );
                Departmentline::findOrFail($id)
                ->update($form_data);

                return redirect()->route('departmentlines');
            } else{
                $form_data = array(
                    'status' =>  '2',
                );
                Departmentline::findOrFail($id)
                ->update($form_data);

                return redirect()->route('departmentlines');
            }

        }

        public function LinesDepartment(Request $request)
        {
            $lines = DepartmentLine::where('department_id', $request->department_id)
                                ->where('status', 1)
                                ->get(['id', 'line']);
            
            return response()->json($lines);
        }

}
