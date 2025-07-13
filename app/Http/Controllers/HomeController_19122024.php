<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Attendance;
use DB;
use Carbon\Carbon;
use DateTime;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         $today = Carbon::now()->format('Y-m-d');
         $empcount = DB::table('employees')->where('deleted', 0)->where('is_resigned', 0)->count();
        //  $todaycount = Attendance::where('date','2023-09-18')->groupBy('date','emp_id')->count();

        // today attendance count
         $todaycount = DB::table('attendances')
        ->select('date', 'emp_id')
        ->where('date', $today)
        ->groupBy('date', 'emp_id')
        ->get()
        ->count();

        // today late attendance count
        $late_times = DB::table('late_types')->where('id', 2)->first();
        $todaylatecount = DB::table('attendances')
        ->select('date', 'emp_id')
        ->where('date', $today)
        ->where('timestamp','>', $today. ' ' . $late_times->time_from)
        ->groupBy('date', 'emp_id')
        ->get()
        ->count();

        // --------------------------------------------------------------------------------------------------------------

        // get today daybefore day on attendance
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

         // yesterday attendance count
         $yesterdaycount = DB::table('attendances')
        ->select('date', 'emp_id')
        ->where('date', $yesterdayDate)
        ->groupBy('date', 'emp_id')
        ->get()
        ->count();

        // yesterday late attendance count
        $yesterdaylatecount = DB::table('attendances')
        ->select('date', 'emp_id')
        ->where('date', $yesterdayDate)
        ->havingRaw('MIN(attendances.timestamp) > ?', [$yesterdayDate . ' ' . $late_times->time_from])
        ->groupBy('date', 'emp_id')
        ->get()
        ->count();

        return view('home',compact('empcount','todaycount','todaylatecount','yesterdaycount','yesterdaylatecount'));
    }

    public function department_attendance(){
        $today = Carbon::now()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $today)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the today.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_lateattendance(){
        $today = Carbon::now()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();
        $late_times = DB::table('late_types')->where('id', 2)->first();
        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $today)
        ->where('attendances.timestamp','>', $today. ' ' . $late_times->time_from)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the today.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_absent(){
        $today = Carbon::now()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('date', '=', $today)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $employeedata= DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();

        $employeeMap = [];
        foreach ($employeedata as $employee) {
            $employeeMap[$employee->emp_id] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_department' => $employee->emp_department
            ];
        }
       
        $uniqueEmployeeData = [];
        foreach ($attendance as $attendant) {
            $employeeId = $attendant->emp_id;
            if (isset($employeeMap[$employeeId])) {
                unset($employeeMap[$employeeId]);
            }
        }
    
        foreach ($employeeMap as $employeeId => $employeeData) {
            $uniqueEmployeeData[] = $employeeData;
        }

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($uniqueEmployeeData as $employee) {
            $departmentId = $employee['emp_department'];

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee['emp_id'],
                    'emp_name_with_initial' => $employee['emp_name_with_initial']
                ];
            }
        }


            $htmlTables = '';

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

//--------------------------------------------------------------------------------
    // yesterday attendance part

    public function department_yesterdayattendance(){
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $yesterdayDate)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));
            $last_time = date('H:i', strtotime($employee->lasttimestamp));

            if($first_time==$last_time){
                $last_time='00-00';
            }

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time,
                    'lasttimestamp' => $last_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th><th>Out Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '<td>' . $employee['lasttimestamp'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the yesterday.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_yesterdaylateattendance(){
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();
        $late_times = DB::table('late_types')->where('id', 2)->first();
        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial', 
            'employees.emp_department', 
            DB::raw('MIN(attendances.timestamp) as first_checkin'), 
            DB::raw('MAX(attendances.timestamp) as lasttimestamp')
        )
        ->where('attendances.date', '=', $yesterdayDate)
        ->havingRaw('MIN(attendances.timestamp) > ?', [$yesterdayDate . ' ' . $late_times->time_from])
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($attendance as $employee) {
            $departmentId = $employee->emp_department;
            $first_time = date('H:i', strtotime($employee->first_checkin));
            $last_time = date('H:i', strtotime($employee->lasttimestamp));

            if($first_time==$last_time){
                $last_time='00-00';
            }

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee->emp_id,
                    'emp_name_with_initial' => $employee->emp_name_with_initial,
                    'first_checkin' => $first_time,
                    'lasttimestamp' => $last_time
                ];
            }
        }


            $htmlTables = '';

            if ($attendance->count() > 0) {

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th><th>In Time</th><th>Out Time</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '<td>' . $employee['first_checkin'] . '</td>';
                        $htmlTables .= '<td>' . $employee['lasttimestamp'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }
            }else {
                $htmlTables = '<p>No attendance records found for the yesterday.</p>';
            }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

    public function department_yesterdayabsent(){
        $yesterdayDate = Carbon::now()->subDay()->format('Y-m-d');

        $departmentdata = DB::table('departments')
        ->select('id', 'name') 
        ->get()
        ->toArray();

        $attendance= DB::table('attendances')
        ->leftjoin('employees', 'attendances.emp_id', '=', 'employees.emp_id')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('date', '=', $yesterdayDate)
        ->groupBy('attendances.date','attendances.emp_id')
        ->get();

        $employeedata= DB::table('employees')
        ->select('employees.emp_id', 'employees.emp_name_with_initial','employees.emp_department') 
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();

        $employeeMap = [];
        foreach ($employeedata as $employee) {
            $employeeMap[$employee->emp_id] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_department' => $employee->emp_department
            ];
        }
       
        $uniqueEmployeeData = [];
        foreach ($attendance as $attendant) {
            $employeeId = $attendant->emp_id;
            if (isset($employeeMap[$employeeId])) {
                unset($employeeMap[$employeeId]);
            }
        }
    
        foreach ($employeeMap as $employeeId => $employeeData) {
            $uniqueEmployeeData[] = $employeeData;
        }

        $departmentMap = [];
        foreach ($departmentdata as $department) {
            $departmentMap[$department->id] = $department->name;
        }

        $employeesByDepartment = [];
        foreach ($uniqueEmployeeData as $employee) {
            $departmentId = $employee['emp_department'];

            if (isset($departmentMap[$departmentId])) {
                if (!isset($employeesByDepartment[$departmentMap[$departmentId]])) {
                    $employeesByDepartment[$departmentMap[$departmentId]] = [];
                }
                
                $employeesByDepartment[$departmentMap[$departmentId]][] = [
                    'emp_id' => $employee['emp_id'],
                    'emp_name_with_initial' => $employee['emp_name_with_initial']
                ];
            }
        }


            $htmlTables = '';

                foreach ($employeesByDepartment as $departmentName => $employees) {
                    $count=1;
                    $htmlTables .= '<table class="table table-striped table-bordered table-sm small">';

                    $htmlTables .= '<h5>' . $departmentName . '</h5>';
                    $htmlTables .= '<tr><th>#</th><th>Employee ID</th><th>Employee Name with Initial</th></tr>';
                
                    foreach ($employees as $employee) {
                        $htmlTables .= '<tr>';
                        $htmlTables .= '<td>' . $count . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_id'] . '</td>';
                        $htmlTables .= '<td>' . $employee['emp_name_with_initial'] . '</td>';
                        $htmlTables .= '</tr>';

                        $count=$count+1;
                    }
                    $htmlTables .= '</table>';
                    $htmlTables .= '<hr style="border-top: 1px solid black;">';
                }

        
        return response() ->json(['result'=>  $htmlTables]);

    }

//----------------------------------------------------------------------------------

    public function getAttendentChart(Request $request)
    {


        $data = DB::table('attendances')
            ->join('employees', 'attendances.emp_id', '=', 'employees.emp_id')
            ->select('attendances.date', DB::raw('COUNT(DISTINCT attendances.uid) as count'))
            ->where('employees.deleted', 0)
            ->groupBy('attendances.date')
            ->limit(30)
            ->orderBy('attendances.date', 'desc')
            ->get();

        return response()->json($data);

    }
}
