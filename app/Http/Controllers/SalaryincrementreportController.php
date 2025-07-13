<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Remuneration;
use Auth;
use DateTime;
use DB;

class SalaryincrementreportController extends Controller
{
      public function index()
    {
        $permission = Auth::user()->can('Increment-Detail-report');
        if (!$permission) {
            abort(403);
        }
       $remuneration = Remuneration::where(['remuneration_cancel'=>0, 'allocation_method'=>'FIXED'])->orderBy('id', 'asc')->get();
        return view('Payroll.salaryIncrement.salaryIncrementreport', compact('remuneration'));
    }

     public function generatesalaryincrementreport(Request $request)
        {
            $department = $request->get('department');
            $remunerationtype = $request->get('remunerationtype');
           
            $currentYear = date('Y');
            $today = date('Y-m-d');

            $query = DB::table('salary_increments as si')
                ->leftjoin('payroll_profiles as pp', 'si.payroll_profile_id', '=', 'pp.id')
                ->leftjoin('employees as e', 'pp.emp_id', '=', 'e.id')
                ->leftjoin('remunerations as r', 'si.remuneration_id', '=', 'r.id')
                ->whereYear('si.effective_date', $currentYear)
                ->where('si.increment_cancel', 0)
                ->where('e.emp_department', $department)
                ->select(
                    'e.emp_name_with_initial',
                    'e.emp_department',
                    'e.emp_id',
                    'e.emp_etfno',
                    'r.remuneration_name',
                    'r.remuneration_type',
                    'pp.basic_salary as current_salary',
                    'si.increment_value',
                    'si.effective_date',
                    'si.remuneration_id'
                );

          

                if (!empty($remunerationtype)) {
                        $query->where('si.remuneration_id', $remunerationtype);
                    }

                $increments = $query->get();

            $datareturn = [];
            foreach ($increments as $increment) {
                $datareturn[] = [
                    'emp_id' => $increment->emp_id,
                    'emp_name_with_initial' => $increment->emp_name_with_initial,
                    'remuneration_name' => $increment->remuneration_name,
                    'effective_date' => $increment->effective_date,
                    'increment_value' => $increment->increment_value
                ];
            }

            return response()->json([
                'data' => $datareturn
            ]);
        }

}
