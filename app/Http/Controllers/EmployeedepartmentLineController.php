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

        $permission = Auth::user()->can('Employee-Carder-Report');
        if(!$permission)
        {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $department = $request->get('department');
        $reportdate = $request->get('reportdate');

        $html ='';

        if ( $department == 1){
            $html = '<div class="row mb-2"> 
                        <div class="col-md-4">
                            <button type="button" class="btn btn-sm btn-outline-danger pdf-btn" onclick="generatePDF();"> Download PDF 
                            </button> 
                        </div>
                     </div>';
            $html .= '<table class="table table-sm table-hover" id="production_carderreport_table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Production</th>';
            $html .= '<th colspan ="4">MO</th>';
            $html .= '<th colspan ="4">Helper</th>';
            $html .= '<th colspan ="4">Carder capacity summary</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $html .= '<tr>';
            $html .= '<td>Section</td>';
            $html .= '<td>BUD, CARDER</td>';
            $html .= '<td>ACTUAL</td>';
            $html .= '<td>PRESENT</td>';
            $html .= '<td>AB</td>';
            $html .= '<td>BUD, CARDER</td>';
            $html .= '<td>ACTUAL</td>';
            $html .= '<td>PRESENT</td>';
            $html .= '<td>AB</td>';
            $html .= '<td>TOTAL BUD, CARDER</td>';
            $html .= '<td>TOTAL CARDER</td>';
            $html .= '<td>AB</td>';
            $html .= '<td>PRESENT</td>';
            $html .= '</tr>';


                // get supervisor details
           $supervisorCounts = DB::table('employee_line as el')
            ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
            ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
            ->leftjoin('attendances as a', function($join) use ($reportdate) {
                $join->on('el.emp_id', '=', 'a.emp_id')
                    ->where('a.date', '=', $reportdate)
                    ->whereNull('a.deleted_at');
            })
            ->where('dl.department_id', $department)
            ->where('el.date', $reportdate)
            ->where('el.status', 1)
            ->where('e.emp_job_code', 48)
            ->selectRaw('COUNT(DISTINCT el.emp_id) as total_supervisors')
            ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_supervisors')
            ->first();

            $totalSupervisors = $supervisorCounts->total_supervisors ?? 0;
            $presentSupervisors = $supervisorCounts->present_supervisors ?? 0;
            $absentSupervisors = $totalSupervisors - $presentSupervisors;

            $html .= '<tr>';
            $html .= '<td>Supervisors</td>';
            $html .= '<td>0</td>'; 
            $html .= '<td>0</td>';
            $html .= '<td>0</td>';
            $html .= '<td>0</td>';
            $html .= '<td>0</td>'; 
            $html .= '<td>0</td>';
            $html .= '<td>0</td>';
            $html .= '<td>0</td>';
            $html .= '<td>0</td>';
            $html .= '<td>' . $totalSupervisors . '</td>'; 
            $html .= '<td>' . $absentSupervisors . '</td>'; 
            $html .= '<td>' . $presentSupervisors . '</td>';
            $html .= '</tr>';


            $totalactualmo =0;
            $totalactualmopresent =0;
            $totalactualmoabsent =0;
            $totalactualhelper =0;
            $totalactualhelperabsent =0;
            $totalactualhelperpresent =0;
            $totalactualcarder =0;
            $totalactualcarderab =0;
            $totalactualcarderpresent =0;

            $departmentLines = DB::table('department_lines')
                ->where('department_id', $department)
                ->where('status', 1) 
                ->select('department_lines.*')
                ->orderBy('line')
                ->get();

            foreach ($departmentLines as $line) {

            // machine operators count
            $machineoperatorscount = DB::table('employee_line as el')
            ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
            ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
            ->leftjoin('attendances as a', function($join) use ($reportdate) {
                $join->on('el.emp_id', '=', 'a.emp_id')
                    ->where('a.date', '=', $reportdate)
                    ->whereNull('a.deleted_at');
            })
            ->where('el.line_id', $line->id)
            ->where('el.date', $reportdate)
            ->where('el.status', 1)
            ->where('e.emp_job_code', 49)
            ->selectRaw('COUNT(DISTINCT el.emp_id) as total_mocount')
            ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_mocount')
            ->first();

            $totalmachineoperators = $machineoperatorscount->total_mocount ?? 0;
            $presentmachineoperators = $machineoperatorscount->present_mocount ?? 0;
            $absentmachineoperators = $totalmachineoperators - $presentmachineoperators;


            // machine operators count
            $helperscount = DB::table('employee_line as el')
            ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
            ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
            ->leftjoin('attendances as a', function($join) use ($reportdate) {
                $join->on('el.emp_id', '=', 'a.emp_id')
                    ->where('a.date', '=', $reportdate)
                    ->whereNull('a.deleted_at');
            })
            ->where('el.line_id', $line->id)
            ->where('el.date', $reportdate)
            ->where('el.status', 1)
            ->where('e.emp_job_code', 9)
            ->selectRaw('COUNT(DISTINCT el.emp_id) as total_helperscount')
            ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_helperscount')
            ->first();

            $totalhelpers = $helperscount->total_helperscount ?? 0;
            $presenthelpers = $helperscount->present_helperscount ?? 0;
            $absenthelpers = $totalhelpers - $presenthelpers;

            // Accumulate totals
            $totalactualmo += $totalmachineoperators;
            $totalactualmopresent += $presentmachineoperators;
            $totalactualmoabsent += $absentmachineoperators;
            $totalactualhelper += $totalhelpers;
            $totalactualhelperpresent += $presenthelpers;
            $totalactualhelperabsent += $absenthelpers;


            $totalcarder =  $totalmachineoperators + $totalhelpers;
            $totalabsent =  $absentmachineoperators +  $absenthelpers;
            $totalpresent = $presentmachineoperators +  $presenthelpers; 


            $totalactualcarder += $totalcarder;
            $totalactualcarderab += $totalabsent;
            $totalactualcarderpresent += $totalpresent;

            $html .= '<tr>';
            $html .= '<td>' . $line->line . '</td>';
            $html .= '<td>0</td>'; 
            $html .= '<td>' . $totalmachineoperators . '</td>';
            $html .= '<td>' . $presentmachineoperators . '</td>';
            $html .= '<td>' . $absentmachineoperators . '</td>';
            $html .= '<td>0</td>'; 
            $html .= '<td>' . $totalhelpers . '</td>';
            $html .= '<td>' . $presenthelpers . '</td>';
            $html .= '<td>' . $absenthelpers . '</td>';
            $html .= '<td>0</td>';
            $html .= '<td>' . $totalcarder . '</td>'; 
            $html .= '<td>' . $totalabsent . '</td>'; 
            $html .= '<td>' . $totalpresent . '</td>'; 
            $html .= '</tr>';

            }


            $html .= '<tr style="font-weight:bold;">';
            $html .= '<td>Total</td>';
            $html .= '<td>0</td>'; 
            $html .= '<td>' . $totalactualmo . '</td>';
            $html .= '<td>' . $totalactualmopresent . '</td>';
            $html .= '<td>' . $totalactualmoabsent . '</td>';
            $html .= '<td>0</td>'; 
            $html .= '<td>' . $totalactualhelper . '</td>';
            $html .= '<td>' . $totalactualhelperpresent . '</td>';
            $html .= '<td>' . $totalactualhelperabsent . '</td>';
            $html .= '<td>0</td>';
            $html .= '<td>' . $totalactualcarder . '</td>'; 
            $html .= '<td>' . $totalactualcarderab . '</td>'; 
            $html .= '<td>' . $totalactualcarderpresent . '</td>'; 
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';

        } elseif($department == 11 || $department == 3 || $department == 26 || $department == 4){

             $departmentname = DB::table('departments')
                ->where('id', $department)
                ->select('departments.*')
                ->first();

                  $namedepartment = $departmentname->name;
             $html = '<div class="row mb-2"> 
                        <div class="col-md-4">
                            <button type="button" class="btn btn-sm btn-outline-danger pdf-btn" onclick="generatePDFop2();"> Download PDF 
                            </button> 
                        </div>
                     </div>';
            $html .= '<table class="table table-sm table-hover" id="department2_carderreport_table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th rowspan ="2">' . $namedepartment . '</th>';
            $html .= '<th rowspan ="2">Day Actual</th>';
            $html .= '<th rowspan ="2">Night Actual</th>';
            $html .= '<th colspan = "2" >Day</th>';
            $html .= '<th colspan = "2" >Night</th>';
            $html .= '</tr>';
            $html .= '<tr>'; 
            $html .= '<th>Present</th>';
            $html .= '<th>AB</th>';
            $html .= '<th>Present</th>';
            $html .= '<th>AB</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
           
            $executiveCounts = DB::table('employee_line as el')
            ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
            ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
            ->leftjoin('attendances as a', function($join) use ($reportdate) {
                $join->on('el.emp_id', '=', 'a.emp_id')
                    ->where('a.date', '=', $reportdate)
                    ->whereNull('a.deleted_at');
            })
            ->where('dl.department_id', $department)
            ->where('el.date', $reportdate)
            ->where('el.status', 1)
            ->where('e.emp_job_code', 48)
            ->selectRaw('COUNT(DISTINCT el.emp_id) as total_executive')
            ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_executive')
            ->first();

            $totalexecutive = $executiveCounts->total_executive ?? 0;
            $presentexecutive = $executiveCounts->present_executive ?? 0;
            $absentexecutive = $totalexecutive - $presentexecutive;

            $html .= '<tr>';
            $html .= '<td>Executive</td>';
            $html .= '<td>' . $totalexecutive . '</td>'; 
            $html .= '<td>0</td>'; 
            $html .= '<td>' . $presentexecutive . '</td>'; 
            $html .= '<td>' . $absentexecutive . '</td>'; 
            $html .= '<td>0</td>'; 
            $html .= '<td>0</td>'; 
            $html .= '</tr>';



            $totalactualday =0;
            $totalactualnight =0;
            $totaldayabsent =0;
            $totaldaypresent =0;
            $totalnightabsent =0;
            $totalnightpresent =0;

            $departmentLines = DB::table('department_lines')
                ->where('department_id', $department)
                ->where('status', 1) 
                ->select('department_lines.*')
                ->orderBy('line')
                ->get();


                 foreach ($departmentLines as $line) {

                     // cardrs count
                        $cardercount = DB::table('employee_line as el')
                        ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
                        ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
                        ->leftjoin('attendances as a', function($join) use ($reportdate) {
                            $join->on('el.emp_id', '=', 'a.emp_id')
                                ->where('a.date', '=', $reportdate)
                                ->whereNull('a.deleted_at');
                        })
                        ->where('el.line_id', $line->id)
                        ->where('el.date', $reportdate)
                        ->where('el.status', 1)
                        ->where('e.emp_job_code'!= 48)
                        ->selectRaw('COUNT(DISTINCT el.emp_id) as total_cardercount')
                        ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_cardercount')
                        ->first();

                        $totalcarders = $cardercount->total_cardercount ?? 0;
                        $presencarders = $cardercount->present_cardercount ?? 0;
                        $absentcarders = $totalcarders - $presencarders;


                        $totalactualday += $totalcarders;
                        $totaldayabsent += $absentcarders;
                        $totaldaypresent += $presencarders;

                        $html .= '<tr>';
                        $html .= '<td>' . $line->line . '</td>';
                        $html .= '<td>' . $totalcarders . '</td>';
                        $html .= '<td>0</td>'; 
                        $html .= '<td>' . $presencarders . '</td>';
                        $html .= '<td>' . $absentcarders . '</td>';
                        $html .= '<td>0</td>'; 
                        $html .= '<td>0</td>';
                        $html .= '</tr>';

                 }

                  $totalactualday += $totalexecutive;
                  $totaldayabsent += $absentexecutive;
                  $totaldaypresent += $presentexecutive;


            $html .= '<tr style="font-weight:bold;">';
            $html .= '<td>Total</td>';
            $html .= '<td>' . $totalactualday . '</td>';
            $html .= '<td>' . $totalactualnight . '</td>'; 
            $html .= '<td>' . $totaldaypresent . '</td>';
            $html .= '<td>' . $totaldayabsent . '</td>';
            $html .= '<td>' . $totalnightpresent . '</td>'; 
            $html .= '<td>' . $totalnightabsent . '</td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';

        }else{

             $departmentname = DB::table('departments')
                ->where('id', $department)
                ->select('departments.*')
                ->first();

                  $namedepartment = $departmentname->name;

            $html = '<div class="row mb-2"> 
                            <div class="col-md-4">
                                <button type="button" class="btn btn-sm btn-outline-danger pdf-btn" onclick="generatePDFop3();"> Download PDF 
                                </button> 
                            </div>
                        </div>';
                $html .= '<table class="table table-sm table-hover" id="department2_carderreport_table">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th>' . $namedepartment . '</th>';
                $html .= '<th>ACTUAL CADRE</th>';
                $html .= '<th>Present</th>';
                $html .= '<th>AB</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
           
            $executiveCounts = DB::table('employee_line as el')
            ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
            ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
            ->leftjoin('attendances as a', function($join) use ($reportdate) {
                $join->on('el.emp_id', '=', 'a.emp_id')
                    ->where('a.date', '=', $reportdate)
                    ->whereNull('a.deleted_at');
            })
            ->where('dl.department_id', $department)
            ->where('el.date', $reportdate)
            ->where('el.status', 1)
            ->where('e.emp_job_code', 48)
            ->selectRaw('COUNT(DISTINCT el.emp_id) as total_executive')
            ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_executive')
            ->first();

            $totalexecutive = $executiveCounts->total_executive ?? 0;
            $presentexecutive = $executiveCounts->present_executive ?? 0;
            $absentexecutive = $totalexecutive - $presentexecutive;

            $html .= '<tr>';
            $html .= '<td>Executive</td>';
            $html .= '<td>' . $totalexecutive . '</td>'; 
            $html .= '<td>' . $presentexecutive . '</td>'; 
            $html .= '<td>' . $absentexecutive . '</td>'; 
            $html .= '</tr>';



            $totalactual =0;
            $totalabsent =0;
            $totalpresent =0;

            $departmentLines = DB::table('department_lines')
                ->where('department_id', $department)
                ->where('status', 1) 
                ->select('department_lines.*')
                ->orderBy('line')
                ->get();

                foreach ($departmentLines as $line) {

                     // cardrs count
                        $cardercount = DB::table('employee_line as el')
                        ->leftjoin('employees as e', 'el.emp_id', '=', 'e.emp_id')
                        ->leftjoin('department_lines as dl', 'el.line_id', '=', 'dl.id')
                        ->leftjoin('attendances as a', function($join) use ($reportdate) {
                            $join->on('el.emp_id', '=', 'a.emp_id')
                                ->where('a.date', '=', $reportdate)
                                ->whereNull('a.deleted_at');
                        })
                        ->where('el.line_id', $line->id)
                        ->where('el.date', $reportdate)
                        ->where('el.status', 1)
                        ->where('e.emp_job_code'!= 48)
                        ->selectRaw('COUNT(DISTINCT el.emp_id) as total_cardercount')
                        ->selectRaw('COUNT(DISTINCT CASE WHEN a.id IS NOT NULL THEN el.emp_id END) as present_cardercount')
                        ->first();

                        $totalcarders = $cardercount->total_cardercount ?? 0;
                        $presencarders = $cardercount->present_cardercount ?? 0;
                        $absentcarders = $totalcarders - $presencarders;

                        $totalactual += $totalcarders;
                        $totalabsent += $absentcarders;
                        $totalpresent += $presencarders;

                        $html .= '<tr>';
                        $html .= '<td>' . $line->line . '</td>';
                        $html .= '<td>' . $totalcarders . '</td>';
                        $html .= '<td>' . $presencarders . '</td>';
                        $html .= '<td>' . $absentcarders . '</td>';
                        $html .= '</tr>';

                 }

                  $totalactual += $totalexecutive;
                  $totalabsent += $absentexecutive;
                  $totalpresent += $presentexecutive;


                    $html .= '<tr style="font-weight:bold;">';
                    $html .= '<td>Total</td>';
                    $html .= '<td>' . $totalactual . '</td>';
                    $html .= '<td>' . $totalpresent . '</td>'; 
                    $html .= '<td>' . $totalabsent . '</td>';
                    $html .= '</tr>';
                    $html .= '</tbody>';
                    $html .= '</table>';

                    // summary tables
                    $html .= '<br><br>';
                    $html .= '<table class="table table-sm table-hover" id="summary_carderreport_table1">';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th >Night Total</th>';
                    $html .= '<th>ACTUAL Carder</th>';
                    $html .= '<th >Present Carder</th>';
                    $html .= '<th >AB Carder</th>';
                    $html .= '<th ></th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';

                    $html .= '<tr>';
                    $html .= '<td>Indirect</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>'; 
                    $html .= '<td></td>';
                    $html .= '<td rowspan ="3"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td>Direct</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>'; 
                    $html .= '<td></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td>Total</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>'; 
                    $html .= '<td></td>';
                    $html .= '</tr>';
                    $html .= '</tbody>';
                    $html .= '</table>';


                    $html .= '<br><br>';
                    $html .= '<table class="table table-sm table-hover" id="summary_carderreport_table2">';
                    $html .= '<thead>';
                    $html .= '<tr>';
                    $html .= '<th >Today Total</th>';
                    $html .= '<th>ACTUAL Carder</th>';
                    $html .= '<th >Present Carder</th>';
                    $html .= '<th >AB Carder</th>';
                    $html .= '<th ></th>';
                    $html .= '</tr>';
                    $html .= '</thead>';
                    $html .= '<tbody>';
                    $html .= '<tr>';
                    $html .= '<td>Indirect</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>'; 
                    $html .= '<td></td>';
                    $html .= '<td rowspan ="3"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td>Direct</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>'; 
                    $html .= '<td></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td>Total</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>'; 
                    $html .= '<td></td>';
                    $html .= '</tr>';
                    $html .= '</tbody>';
                    $html .= '</table>';
        }


          $response = ['html' => $html];

        return response()->json($response);
    }


}
