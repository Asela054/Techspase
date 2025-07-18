<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Request;

class Employeelateattenadnaceminites extends Model
{
    protected $table = 'employee_late_attendance_minites';
    protected $primaryKey = 'id';

    protected $fillable = [
        'attendance_id',
        'emp_id',
        'attendance_date',
        'minites_count'
    ];


    public function get_lateminitescount($emp_id, $month ,$closedate)
    {
        $lateminites = Employeelateattenadnaceminites::where('emp_id', $emp_id)
                            ->where('attendance_date', 'like', $month.'%')
							->where('attendance_date', '<=', $closedate)
                            ->sum('minites_count');
        return $lateminites;
    }


    public function NopayAmountCal($empid,$work_days,$leave_days,$no_pay_days,$normal_ot_hours, $double_ot_hours){
			$emp_etfno = $empid;
            $emp_work=$work_days;
            $emp_leave=$leave_days;
            $emp_nopay=$no_pay_days; 
            $emp_ot_i=$normal_ot_hours; 
            $emp_ot_ii= $double_ot_hours;
			
			$sql_info = "SELECT payroll_profiles.id as payroll_profile_id, payroll_profiles.basic_salary, payroll_profiles.day_salary, payroll_process_types.pay_per_day FROM `payroll_profiles` inner join payroll_process_types on payroll_profiles.payroll_process_type_id=payroll_process_types.id WHERE payroll_profiles.emp_id=?";
			$profiles = DB::select($sql_info, [$emp_etfno]);
			
                $employeePayslip = EmployeePayslip::where(['payroll_profile_id' => $profiles[0]->payroll_profile_id])
                    ->latest()
                    ->first();
                    
                $emp_payslip_no = empty($employeePayslip) ? 1 : ($employeePayslip->emp_payslip_no + 1);

			$empjobcategoryinfo = DB::table('employees')
				->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
				->select('job_categories.emp_payroll_workdays', 'job_categories.emp_payroll_workhrs')
				->where('employees.id', $empid)
				->first();
          
			
			
			/**/
			// DB::enableQueryLog();
			$sql_main="SELECT fig_name, fig_group, fig_group_title, fig_base_ratio, fig_value, fig_hidden, epf_payable, remuneration_pssc FROM (SELECT drv_figs.fig_name, drv_figs.fig_group, drv_figs.fig_group_title, drv_figs.fig_value AS fig_base_ratio, COALESCE(NULLIF(drv_figs.fig_value*(((drv_figs.fig_group='FIXED') * ? * ?) + ((drv_figs.fig_group='FIXED') * (1 - ?)) + (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (? * drv_figs.nopay_payable * (drv_figs.fig_group='BASIC')) + (? * (drv_figs.fig_group='OTHRS1')) + (? * (drv_figs.fig_group='OTHRS2')))*drv_figs.pay_per_day, 0), (drv_figs.fig_value*drv_figs.fig_revise)) AS fig_value, drv_figs.fig_hidden, drv_figs.epf_payable, drv_figs.remuneration_pssc FROM (SELECT 'Basic' AS fig_name, 'BASIC' AS fig_group, 'BASIC' AS fig_group_title, COALESCE(NULLIF(CAST(?*? AS DECIMAL(10,2)), 0), ?) AS fig_value, ? AS pay_per_day, 1 AS fig_revise, 0 AS fig_hidden, 1 AS epf_payable, 1 AS work_payable, 1 AS nopay_payable, 'BASIC' AS remuneration_pssc UNION ALL SELECT 'No pay' AS fig_name, 'BASIC' AS fig_group, 'NOPAY' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 1 AS nopay_payable, 'NOPAY' AS remuneration_pssc UNION ALL SELECT 'Normal OT' AS fig_name, 'OTHRS1' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS1' AS remuneration_pssc UNION ALL SELECT 'Double OT' AS fig_name, 'OTHRS2' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS2' AS remuneration_pssc UNION ALL select drv_allfacility.remuneration_name AS fig_name, IFNULL(drv_allfacility.fig_group, 'BASIC') AS fig_group, 'FACILITY' AS fig_group_title, (IFNULL(drv_dayfacility.pre_eligible_amount, drv_empfacility.new_eligible_amount)*drv_allfacility.value_group) AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, drv_allfacility.epf_payable, 1 AS work_payable, 0 AS nopay_payable, drv_allfacility.pssc AS remuneration_pssc from (SELECT `remuneration_id`, `new_eligible_amount` FROM `remuneration_profiles` WHERE `payroll_profile_id`=? AND `remuneration_signout`=0) AS drv_empfacility INNER JOIN (SELECT id, remuneration_name, remuneration_type, value_group, epf_payable, allocation_method AS fig_group, payslip_spec_code AS pssc FROM remunerations WHERE allocation_method='FIXED' AND remuneration_cancel=0) AS drv_allfacility ON drv_empfacility.remuneration_id=drv_allfacility.id LEFT OUTER JOIN (SELECT remuneration_id, pre_eligible_amount, 'FIXED' AS fig_group FROM remuneration_eligibility_days WHERE ? BETWEEN min_days AND max_days) AS drv_dayfacility ON drv_allfacility.id=drv_dayfacility.remuneration_id) AS drv_figs UNION ALL SELECT drv_docs.fig_name, drv_docs.fig_group, drv_docs.fig_group_title, drv_docs.fig_value AS fig_base_ratio, drv_docs.fig_value, drv_docs.fig_hidden, drv_docs.epf_payable, drv_docs.remuneration_pssc FROM (SELECT remunerations.remuneration_name AS fig_name, 'ADDITION' AS fig_group, 'ADDITION' AS fig_group_title, (employee_term_payments.payment_amount*remunerations.value_group) AS fig_value, 0 AS fig_hidden, remunerations.epf_payable, remunerations.payslip_spec_code AS remuneration_pssc FROM (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS employee_term_payments INNER JOIN remunerations ON employee_term_payments.remuneration_id=remunerations.id) AS drv_docs) AS drv_main";
			$employee = DB::select($sql_main, [$emp_work, $profiles[0]->pay_per_day, $profiles[0]->pay_per_day, $emp_work, $emp_leave, $emp_nopay, $emp_ot_i, $emp_ot_ii, $profiles[0]->day_salary, $profiles[0]->pay_per_day, $profiles[0]->basic_salary, $profiles[0]->pay_per_day, ($profiles[0]->day_salary*-1), ($profiles[0]->day_salary/8), (($profiles[0]->day_salary*1)/8), $profiles[0]->payroll_profile_id, $emp_work, $profiles[0]->payroll_profile_id, $emp_payslip_no]);
			// $queryLog = DB::getQueryLog();
			// $query = end($queryLog); // Get the last executed query

			// // Replace bindings in the query
			// $sql = vsprintf(str_replace('?', "'%s'", $query['query']), $query['bindings']);

			// dd($sql);
			
			$figs_list = array();
			$epf_payable_tot = 0;
			
			foreach($employee as $r){
				if($r->epf_payable){
					$epf_payable_tot += $r->fig_value;
				}
				
				if(!isset($figs_list[$r->remuneration_pssc])){
					$figs_list[$r->remuneration_pssc]=array(
											'fig_grp_title'=>$r->fig_group_title, 
											'fig_val'=>0, 
											'fig_base_rate'=>$r->fig_base_ratio
										);
				}
				
				$figs_list[$r->remuneration_pssc]['fig_val'] += $r->fig_value;
			}


			
			$payperiod_workdays=$empjobcategoryinfo->emp_payroll_workdays; $payperiod_holidays=0;
			$payperiod_netdays=($payperiod_workdays-$payperiod_holidays)*-1;
			
			$reg_keys = array('NOPAY', 'OTHRS1', 'OTHRS2');
			$reg_cols = array('NOPAY'=>array('fig_premium'=>1, 'key_param'=>$payperiod_netdays), 
							 'OTHRS1'=>array('fig_premium'=>1.5, 'key_param'=>$empjobcategoryinfo->emp_payroll_workhrs), 
							 'OTHRS2'=>array('fig_premium'=>2, 'key_param'=>$empjobcategoryinfo->emp_payroll_workhrs)
						);
			
			
			foreach($figs_list as $k=>$v){
				if(in_array($k, $reg_keys)){
					$units_tot = ($figs_list[$k]['fig_base_rate'] != 0) ? ($figs_list[$k]['fig_val'] / $figs_list[$k]['fig_base_rate']) : 0;
					$new_base_rate = (($epf_payable_tot*$reg_cols[$k]['fig_premium'])/$reg_cols[$k]['key_param']);
					$figs_list[$k]['fig_val']=number_format((float)($new_base_rate*$units_tot), 2, '.', '');
					$figs_list[$k]['fig_base_rate']=number_format((float)$new_base_rate, 2, '.', '');
				}
			}

			// dd(['nopay_val' => $figs_list['NOPAY']['fig_val']]);
			return ['nopay_val' => $figs_list['NOPAY']['fig_val'], 'nopay_base_rate' => $figs_list['NOPAY']['fig_base_rate']];
		
	}

}
