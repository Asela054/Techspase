<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class NightShiftReportController extends Controller
{
    public function nightshiftreport(Request $request)
    {
        $permission = Auth::user()->can('employee-shift-allocation-report');
        if (!$permission) {
            abort(403);
        }
       
        return view('Report.night_shift_report', compact(''));
    }

    public function night_shift_report_list(Request $request)
    {
        $department = $request->department;
        $shift = $request->shift;
        $employee = $request->employee;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = DB::table('employeeshiftdetails')
            ->leftJoin('employeeshifts', 'employeeshifts.id', 'employeeshiftdetails.employeeshift_id')
            ->leftJoin('employees', 'employees.emp_id', 'employeeshiftdetails.emp_id')
            ->leftJoin('departments', 'departments.id', 'employees.emp_department')
            ->where('employeeshiftdetails.status', 1)
            ->where('employeeshifts.status', 1)
            ->where('employees.deleted', 0)
            ->where('employees.status', 1)
            ->where('employees.is_resigned', 0)
            ->select(
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'departments.name AS dept_name',
                'employeeshifts.shift_id', 
                'employeeshifts.date_from'
            )
            ->orderBy('departments.name');

        if ($department) {
            $query->where('employees.emp_department', $department);
        }

        if ($shift) {
            $query->where('employeeshifts.shift_id', $shift);
        }

        if ($employee) {
            $query->where('employeeshiftdetails.emp_id', $employee);
        }

        if ($from_date && $to_date) {
            $query->whereBetween('employeeshifts.date_from', [$from_date, $to_date]);
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('date_from', function ($row) {
                return date('Y-m-d', strtotime($row->date_from));
            })
            ->make(true);
    }
}