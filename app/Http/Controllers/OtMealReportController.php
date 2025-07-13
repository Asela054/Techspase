<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class OtMealReportController extends Controller
{
    public function mealreport(Request $request)
    {

        $permission = Auth::user()->can('employee-ot-allocation-report');
        if (!$permission) {
            abort(403);
        }
        
        return view('Report.ot_meal_report');
    }

    public function meal_report_list(Request $request)
    {
        $route = $request->route;
        $employee = $request->employee;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = DB::table('ot_allocationdetails')
            ->leftjoin('ot_allocation', 'ot_allocation.id', 'ot_allocationdetails.ot_allocation_id')
            ->leftjoin('employees', 'employees.emp_id', 'ot_allocationdetails.emp_id')
            ->leftjoin('departments', 'departments.id', 'employees.emp_department')
            ->where('ot_allocationdetails.status', 1)
            ->where('ot_allocation.status', 1)
            ->where('ot_allocationdetails.meal', 1)
            ->where('employees.deleted', 0)
            ->where('employees.status', 1)
            ->where('employees.is_resigned', 0)
            ->select(
                'employees.emp_id', 
                'employees.emp_name_with_initial',
                'departments.name AS dept_name',
                'ot_allocation.date'
            );

        if ($employee) {
            $query->where('ot_allocationdetails.emp_id', $employee);
        }

        if ($from_date && $to_date) {
            $query->whereBetween('ot_allocation.date', [$from_date, $to_date]);
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
}