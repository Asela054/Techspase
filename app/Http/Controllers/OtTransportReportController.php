<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class OtTransportReportController extends Controller
{
    public function transportreport(Request $request)
    {
        $transportroute = DB::table('transport_routes')->select('id', 'name')->get();

        $permission = Auth::user()->can('employee-ot-allocation-report');
        if (!$permission) {
            abort(403);
        }
        
        return view('Report.ot_transport_report', compact('transportroute'));
    }

    public function transport_report_list(Request $request)
    {
        $route = $request->route;
        $employee = $request->employee;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = DB::table('ot_allocationdetails')
            ->leftjoin('ot_allocation', 'ot_allocation.id', 'ot_allocationdetails.ot_allocation_id')
            ->leftjoin('employees', 'employees.emp_id', 'ot_allocationdetails.emp_id')
            ->leftjoin('departments', 'departments.id', 'employees.emp_department')
            ->leftjoin('transport_vehicles', 'transport_vehicles.id', 'ot_allocationdetails.vehicle_id')
            ->leftjoin('transport_routes', 'transport_routes.id', 'ot_allocationdetails.route_id')
            ->where('ot_allocationdetails.status', 1)
            ->where('ot_allocation.status', 1)
            ->where('ot_allocationdetails.transport', 1)
            ->where('employees.deleted', 0)
            ->where('employees.status', 1)
            ->where('employees.is_resigned', 0)
            ->select(
                'employees.emp_id', 
                'employees.emp_name_with_initial',
                'departments.name AS dept_name',
                'ot_allocation.date',
                'transport_routes.name AS route',
                'transport_vehicles.vehicle_number AS vehicle'
            )
            ->orderBy('transport_routes.name'); 

        if ($route) {
            $query->where('ot_allocationdetails.route_id', $route);
        }

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