<?php

namespace App\Http\Controllers;

use App\Department;
use App\Departmentline;
use App\Employeedepartmentline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class EmployeedepartmentLineController extends Controller
{
    public function index()
    {
        $department= Department::orderBy('id', 'asc')->get();
        $departmentline= Departmentline::orderBy('id', 'asc')->get();
        return view('Employee_line.lineallocation',compact('department','departmentline'));
    }

    public function getemplist(Request $request)
    {
        $departmentline = $request->input('departmentline');
        $linedate = $request->input('linedate');
        $maxDaysToCheck = 30;
        $allocation = null;
        $currentDate = Carbon::parse($linedate);

        
        for ($i = 1; $i < $maxDaysToCheck; $i++) {

            $dateToCheck = $currentDate->copy()->subDays($i)->format('Y-m-d');

        
            $allocation = DB::table('employee_line')
                    ->leftjoin('employees', 'employee_line.emp_id', '=', 'employees.emp_id')
                    ->select('employee_line.*','employee_line.id As allocationid','employees.emp_name_with_initial As emp_name')
                    ->where('employee_line.status',1, 2)
                    ->where('employee_line.line_id', $departmentline)
                    ->where('employee_line.date', $dateToCheck)
                    ->get();
    
            if ($allocation) {
                break; 
            }
        }

        $htmlemployee = '';

        foreach ($allocation as $row) {
            $htmlemployee .= '<tr data-empid="' . $row->emp_id . '">';
            $htmlemployee .= '<td>' . $row->emp_id . '</td>';  
            $htmlemployee .= '<td>' . $row->emp_name . '</td>'; 
            $htmlemployee .= '<td> <button class="btn btn-danger btn-sm delete-row"> <i class="fas fa-trash"></i></button></td>';
            $htmlemployee .= '</tr>';
        }
        return response() ->json(['result'=>  $htmlemployee]);
    }

    public function insert(Request $request)
    {
        $permission = Auth::user()->can('Employee-Lines-create');
        if (!$permission) {
            abort(403);
        }

        $departmentline = $request->input('departmentline');
        $linedate = $request->input('linedate');
        $tableData = $request->input('tableData');

        foreach ($tableData as $rowtabledata) {
            $empid = $rowtabledata['col_1'];

            if(!empty($empid)){
                $line = new Employeedepartmentline();
                $line->emp_id = $empid;
                $line->line_id = $departmentline;
                $line->date = $linedate;
                $line->status = '1';
                $line->created_by = Auth::id();
                $line->updated_by = '0';
                $line->save();
            }
        }
        return response()->json(['success' => 'Employee Line Added successfully.']);
    }

    public function edit(Request $request)
    {
        $permission = Auth::user()->can('Employee-Lines-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('employee_line')
        ->leftjoin('employees', 'employee_line.emp_id', '=', 'employees.emp_id')
        ->select('employee_line.*','employees.emp_name_with_initial As emp_name')
        ->where('employee_line.id', $id)
        ->get(); 

        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function update(Request $request)
    {
        $permission = Auth::user()->can('Employee-Lines-edit');
        if (!$permission) {
            abort(403);
        }

        $editemployee = $request->input('editemployee');
        $editlinedate = $request->input('editlinedate');
        $editdepartmentline = $request->input('editdepartmentline');
        $hidden_id = $request->input('hidden_id');

            $data = array(
                'emp_id' => $editemployee,
                'line_id' => $editdepartmentline,
                'date' => $editlinedate,
                'updated_by' => Auth::id(),
            );
        
            Employeedepartmentline::where('id', $hidden_id)
            ->update($data);

        return response()->json(['success' => 'Employee Line Updated successfully.']);
    }

    public function delete(Request $request){
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Employeedepartmentline::where('id',$id)
        ->update($form_data);

          return response()->json(['success' => 'Job Allocation is Successfully Deleted']);
    }

    public function employeeline_csv(Request $request)
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

            if (count($data) < 3) {
                continue;
            }

            $emp_id = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[0]));
            $raw_date = trim($data[1]);
            $lineid = trim($data[2]);
           
            $date = Carbon::createFromFormat('m/d/Y', $raw_date)->format('Y-m-d');
            $emp = DB::table('employees')
                ->select('emp_id')
                ->where('emp_id', $emp_id)
                ->first();

                if (!$emp) {
                    continue; 
                }

                $line = new Employeedepartmentline();
                $line->emp_id = $emp_id;
                $line->line_id = $lineid;
                $line->date = $date;
                $line->status = '1';
                $line->created_by = Auth::id();
                $line->updated_by = '0';
                $line->save();

        }

        return response()->json(['status' => true, 'msg' => 'CSV Upload successfully.']);
    }
 
    public function carderreport()
    {
        return view('Employee_line.carderreport');
    }

    public function generatecarderreport(Request $request){

        $permission = \Auth::user()->can('Employee-Carder-Report');
        if(!$permission)
        {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $department = $request->get('department');
        $reportdate = $request->get('reportdate');


            $departmentLines = DB::table('department_lines')
            ->where('department_id', $department)
            ->where('status', 1) 
            ->orderBy('line')
            ->get();

        $result = [];

        foreach ($departmentLines as $line) {
            $employees = DB::table('employee_line as el')
            ->join('employees as e', 'el.emp_id', '=', 'e.emp_id')
            ->where('el.line_id', $line->id)
            ->where('el.date', $reportdate)
            ->where('el.status', 1)
            ->select(
                'e.emp_id',
                'e.emp_name_with_initial as name')
            ->orderBy('e.emp_name_with_initial')
            ->get();

            foreach ($employees as $emp) {
                $result[] = [
                    'emp_id' => $emp->emp_id,
                    'name' => $emp->name,
                    'line_name' => $line->line,
                    'line_id' => $line->id
                ];
            }
        }

        return response()->json([
            'data' => $result, 
        ]);
    }


}
