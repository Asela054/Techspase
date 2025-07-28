<?php

namespace App\Http\Controllers;

use App\Coverup_detail;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Datatables;



class CoverupController extends Controller
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
        $permission = Auth::user()->can('Coverup-list');
        if (!$permission) {
            abort(403);
        }
        // $employee = Employee::orderBy('id', 'desc')->get();

        return view('Leave.coverup_details', compact('employee'));
    }

    public function coverup_list_dt(Request $request)
    {
        $permission = Auth::user()->can('Coverup-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');

        $query =  DB::table('coverup_details')
            // ->join('employees as ec', 'coverup_details.emp_id', '=', 'ec.emp_id')
            ->join('employees as e', 'coverup_details.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('coverup_details.*', 'e.emp_name_with_initial as emp_name', 'departments.name as dep_name');

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['e.emp_id' => $employee]);
        }

        if($location != ''){
            $query->where(['e.emp_location' => $location]);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';

                $permission = Auth::user()->can('Coverup-edit');
                if ($permission) {
                    $btn = ' <button name="edit" id="'.$row->id.'"
                            class="edit btn btn-outline-primary btn-sm" style="margin:1px;" type="submit">
                            <i class="fas fa-pencil-alt"></i>
                        </button> ';
                }

                $permission = Auth::user()->can('Coverup-delete');
                if ($permission) {
                    $btn .= '<button type="submit" name="delete" id="'.$row->id.'"
                            class="delete btn btn-outline-danger btn-sm" style="margin:1px;" ><i
                            class="far fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $rules = array(
            'coveringemployee' => '',
            'coverdate' => '',
            'date' => '',
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $coverdate = Carbon::parse($request->input('coverdate'));
        $date = Carbon::parse($request->input('date'));
        $start_time = Carbon::parse($request->input('start_time'));
        $end_time = Carbon::parse($request->input('end_time'));
        
        $diffInMinutes = $start_time->diffInMinutes($end_time);
        $covering_hours = round($diffInMinutes / 60, 2);

        $coverup_detail = new Coverup_detail;
        $coverup_detail->emp_id = $request->input('coveringemployee');
        $coverup_detail->coverdate = $coverdate;
        $coverup_detail->date = $date;
        $coverup_detail->start_time = $start_time;
        $coverup_detail->end_time = $end_time;
        $coverup_detail->covering_hours = $covering_hours; 
        $coverup_detail->created_at = Carbon::now();
        $coverup_detail->save();

        return response()->json(['success' => 'Coverup Details Added Successfully']);
    }


    public function edit($id)
    {
        if (request()->ajax()) {
            $data = Coverup_detail::with('employee')
                ->with('covering_employee')
                ->findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Coverup_detail $coverup_detail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coverup_detail $coverup_detail)
    {

        $rules = array(
            'date' => 'date',
            'coverdate' => '',
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
        );


        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $coverdate = Carbon::parse($request->input('coverdate'));
        $date = Carbon::parse($request->input('date'));
        $start_time = Carbon::parse($request->input('start_time'));
        $end_time = Carbon::parse($request->input('end_time'));
        
        $diffInMinutes = $start_time->diffInMinutes($end_time);
        $covering_hours = round($diffInMinutes / 60, 2);
        
        $form_data = array(
            'emp_id' => $request->input('coveringemployee'),
            'coverdate' => $coverdate,
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'covering_hours' => $covering_hours, 
            'updated_at' => Carbon::now(),
        );

        Coverup_detail::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Covering Details Successfully Updated']);
    }


    public function destroy($id)
    {
        $permission = Auth::user()->can('Coverup-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $data = Coverup_detail::findOrFail($id);
        $data->delete();
    }


    public function covering_csv(Request $request)
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

            if (count($data) < 5) {
                continue;
            }

            $emp_id = trim(preg_replace('/^\xEF\xBB\xBF/', '', $data[0]));
            $coverdate = Carbon::parse(trim($data[1]))->format('Y-m-d');
            $date = Carbon::parse(trim($data[2]))->format('Y-m-d');
            $start_time = Carbon::parse(trim($data[3]));
            $end_time = Carbon::parse(trim($data[4]));
            
            $diffInMinutes = $start_time->diffInMinutes($end_time);
            $covering_hours = round($diffInMinutes / 60, 2);

            $emp = DB::table('employees')
                ->select('emp_id')
                ->where('emp_id', $emp_id)
                ->first();

                if (!$emp) {
                    continue; 
                }

                $coverup_detail = new Coverup_detail;
                $coverup_detail->emp_id =  $emp_id;
                $coverup_detail->coverdate =  $coverdate;
                $coverup_detail->date =  $date;
                $coverup_detail->start_time =$start_time;
                $coverup_detail->end_time =$end_time;
                $coverup_detail->covering_hours = $covering_hours; 
                $coverup_detail->created_at = Carbon::now();
                $coverup_detail->save();
        }

        return response()->json(['status' => true, 'msg' => 'CSV Upload successfully.']);
    }

}
