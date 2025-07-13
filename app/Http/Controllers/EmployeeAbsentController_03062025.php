<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DateInterval;
use DatePeriod;
use DB;

class EmployeeAbsentController extends Controller
{
    public function employee_absent_report()
    {
        $permission = Auth::user()->can('employee-absent-report');
        if (!$permission) {
            abort(403);
        }
        $departments=DB::table('departments')->select('*')->get();
        return view('Report.employee_absent_report',compact('departments'));
    }

    
    public function get_absent_employees(Request $request)
{
    $selectdatefrom = Carbon::parse($request->input('selectdatefrom'));
    $selectdateto = Carbon::parse($request->input('selectdateto'));
    $department = $request->input('department');

    // Get all active employees (filtered by department if needed)
    $query = DB::table('employees')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->leftjoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
        ->select(
            'employees.emp_id', 
            'employees.emp_name_with_initial',
            'employees.emp_department',
            'employees.emp_address',
            'employees.emp_mobile',
            'departments.name AS departmentname',
            'branches.location AS location',
            'job_titles.title AS designation'
        ) 
        ->where('deleted', 0)
        ->where('is_resigned', 0);

    if ($department != 'All') {
        $query->where('employees.emp_department', '=', $department);
    }

    $employees = $query->get();

    // Get all attendance records for the date range
    $attendanceQuery = DB::table('attendances')
        ->select('attendances.emp_id', 'attendances.date')
        ->whereDate('date', '>=', $selectdatefrom->format('Y-m-d'))
        ->whereDate('date', '<=', $selectdateto->format('Y-m-d'));

    if ($department != 'All') {
        $attendanceQuery->join('employees', 'attendances.emp_id', '=', 'employees.emp_id')
            ->where('employees.emp_department', '=', $department);
    }

    $attendances = $attendanceQuery->get()
        ->groupBy('emp_id')
        ->map(function ($items) {
            return $items->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->toArray();
        });

    // Calculate absent days for each employee
    $absentEmployees = [];
    $dateRange = new DatePeriod(
        $selectdatefrom,
        new DateInterval('P1D'),
        $selectdateto->addDay() // Include end date
    );

    foreach ($employees as $employee) {
        $presentDates = $attendances->get($employee->emp_id, []);
        $absentDays = 0;

        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            if (!in_array($dateStr, $presentDates)) {
                $absentDays++;
            }
        }

        if ($absentDays > 0) {
            $absentEmployees[] = [
                'emp_id' => $employee->emp_id,
                'emp_name_with_initial' => $employee->emp_name_with_initial,
                'emp_mobile' => $employee->emp_mobile,
                'emp_address' => $employee->emp_address,
                'departmentname' => $employee->departmentname,
                'location' => $employee->designation,
                'absent_days' => $absentDays
            ];
        }
    }


    return Datatables::of($absentEmployees)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
        })
        ->rawColumns(['action'])
        ->make(true);
}
}
