<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class ShiftExtendReportController extends Controller
{
    public function shiftextendreport(Request $request)
    {
        $permission = Auth::user()->can('employee-shift-extend-report');
        if (!$permission) {
            abort(403);
        }
       
        return view('Report.shift_extend_report', compact(''));
    }

    public function shift_extend_report_list(Request $request)
    {
        $department = $request->department;
        $employee = $request->employee;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = DB::table('shift_extenddetails')
            ->leftJoin('shift_extend', 'shift_extend.id', 'shift_extenddetails.shift_extend_id')
            ->leftJoin('employees', 'employees.emp_id', 'shift_extenddetails.emp_id')
            ->leftJoin('departments', 'departments.id', 'employees.emp_department')
            ->where('shift_extenddetails.status', 1)
            ->where('shift_extend.status', 1)
            ->where('employees.deleted', 0)
            ->where('employees.status', 1)
            ->where('employees.is_resigned', 0)
            ->select(
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'departments.name AS dept_name',
                'shift_extend.date'
            )
            ->orderBy('departments.name');

        if ($department) {
            $query->where('employees.emp_department', $department);
        }

        if ($employee) {
            $query->where('shift_extenddetails.emp_id', $employee);
        }

        if ($from_date && $to_date) {
            $query->whereBetween('shift_extend.date', [$from_date, $to_date]);
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return date('Y-m-d', strtotime($row->date));
            })
            ->make(true);
    }
}