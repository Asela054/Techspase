<?php

namespace App\Http\Controllers\api;

use App\Employee;
use App\EmployeeAbsent;
use App\EmployeeAvailability;
use App\Leave;
use App\ReqGeoLoc;
use App\Routes;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class V2MainController extends Controller
{
    public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public function GetEmployeeProfileDetails(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = Employee::where('emp_id', $request->employee_id)
            ->with('country')
            ->with('company')
            ->with('area')
            ->with('location')
            ->with('department')
            ->with('shiftType')
            ->first();

        $date = date('Y-m-d');
        $employee_availability = EmployeeAvailability::where('emp_id', $request->employee_id)->where('date', $date)->first();

        $emp_avail_data = array();

        if(empty($employee_availability)){
            $emp_avail_data['session'] = '';
        }else{
            $emp_avail_data['session'] = $employee_availability->session;
        }

        $data = array(
            'employee' => $employee,
            'employee_availability' => $emp_avail_data
        );

        if(EMPTY($employee)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($data, 'Employee Details');
    }

    public function GetApprovedUpcomingLeavesForDashboard(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::
            with('leave_type')
            ->where('emp_id', $request->employee_id)
            ->where('status', 'Approved')
            ->orderBy('id','DESC')
            ->take(3)->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leaves');
    }

    public function CheckingForInstructions(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }


        $rec = ReqGeoLoc::where('is_checked', false)
            ->where('emp_id', $request->employee_id)
            ->orderBy('id','DESC')
            ->first();

        if(EMPTY($rec)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($rec, 'CheckingForInstructions');
    }

    public function SendGeoLocation(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'record_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj_geo_loc = ReqGeoLoc::find($request->record_id);

        if(empty($obj_geo_loc)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Record ID']);
        }

        $obj_geo_loc->latitude = $request->latitude;
        $obj_geo_loc->longitude = $request->longitude;
        $obj_geo_loc->is_checked = true;
        $obj_geo_loc->save();

        return (new BaseController)->sendResponse($obj_geo_loc, 'Record Updated');

    }

    public function GetLeaveTypes(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = Employee::where('emp_id', $request->employee_id)->first();
        if($employee == NULL){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Record ID']);
        }

        $emp_join_date = isset($employee->emp_join_date) ? $employee->emp_join_date : false;
        $join_year = Carbon::parse($emp_join_date)->year;
        $join_month = Carbon::parse($emp_join_date)->month;
        $join_date = Carbon::parse($emp_join_date)->day;
        $full_date = '2022-'.$join_month.'-'.$join_date;

        $q_data = DB::table('quater_leaves')
            ->where('from_date', '<', $full_date)
            ->where('to_date', '>', $full_date)
            ->first();

        $like_from_date = date('Y').'-01-01';
        $like_from_date2 = date('Y').'-12-31';

        $total_taken_annual_leaves = DB::table('leaves')
            ->where('leaves.emp_id', '=', $employee->emp_id)
            ->whereBetween('leaves.leave_from', [$like_from_date, $like_from_date2])
            ->where('leaves.leave_type', '=', '1')
            ->get()->toArray();

        $current_year_taken_a_l = 0;
        foreach ($total_taken_annual_leaves as $tta){
            $leave_from = $tta->leave_from;
            $leave_to = $tta->leave_to;

            $leave_from_year = Carbon::parse($leave_from)->year;
            $leave_to_year = Carbon::parse($leave_to)->year;

            if($leave_from_year != $leave_to_year){
                //get current year leaves for that record
                $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                $diff_in_days = $to->diffInDays($from);
                $current_year_taken_a_l += $diff_in_days;

                $jan_data = DB::table('leaves')
                    ->where('leaves.id', '=', $tta->id)
                    ->first();

                $firstDayOfMonth = Carbon::parse($jan_data->leave_to)->startOfMonth()->toDateString();
                $to_t = \Carbon\Carbon::createFromFormat('Y-m-d', $jan_data->leave_to);
                $from_t = \Carbon\Carbon::createFromFormat('Y-m-d', $firstDayOfMonth);

                $diff_in_days_f = $to_t->diffInDays($from_t);
                $current_year_taken_a_l += $diff_in_days_f;

            }else{
                $current_year_taken_a_l += $tta->no_of_days;
            }
        }

        $like_from_date_cas = date('Y').'-01-01';
        $like_from_date2_cas = date('Y').'-12-31';
        $total_taken_casual_leaves = DB::table('leaves')
            ->where('leaves.emp_id', '=', $request->employee_id)
            ->whereBetween('leaves.leave_from', [$like_from_date_cas, $like_from_date2_cas])
            ->where('leaves.leave_type', '=', '2')
            ->get()->toArray();

        $current_year_taken_c_l = 0;
        foreach ($total_taken_casual_leaves as $tta){
            $leave_from = $tta->leave_from;
            $leave_to = $tta->leave_to;

            $leave_from_year = Carbon::parse($leave_from)->year;
            $leave_to_year = Carbon::parse($leave_to)->year;

            if($leave_from_year != $leave_to_year){
                //get current year leaves for that record
                $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                $diff_in_days = $to->diffInDays($from);
                $current_year_taken_c_l += $diff_in_days;
            }else{
                $current_year_taken_c_l += $tta->no_of_days;
            }
        }


        $leave_msg = '';
        $casual_leaves = 0;
        if($join_year == date('Y')){
            $annual_leaves = $q_data->leaves;
            $leave_msg = "Employee can have only a half day per month in Casual Leaves. (Employee joined in current year)";
        }else{
            $annual_leaves = 14;
            $casual_leaves = 7;
        }

        $total_no_of_annual_leaves = $annual_leaves;
        $total_no_of_casual_leaves = $casual_leaves;

        $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
        $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;

        if($employee->emp_status != 2){
            $emp_status = DB::table('employment_statuses')->where('id', $employee->emp_status)->first();
            $leave_msg = 'Casual Leaves - '.$emp_status->emp_status.' Employee can have only a half day per month (Not a permanent employee)';
        }

        $results = array(
            "total_no_of_annual_leaves" => $total_no_of_annual_leaves,
            "total_no_of_casual_leaves" => $total_no_of_casual_leaves,
            "total_taken_annual_leaves" => $current_year_taken_a_l,
            "total_taken_casual_leaves" => $current_year_taken_c_l,
            "available_no_of_annual_leaves" => $available_no_of_annual_leaves,
            "available_no_of_casual_leaves" => $available_no_of_casual_leaves,
            "leave_msg" => $leave_msg
        );
        $annual_arr = array(
            'leave_type_id' => 2,
            'leave_type_name' => 'Annual',
            'total' => $total_no_of_annual_leaves,
            'taken' => $current_year_taken_a_l,
            'available' => $available_no_of_annual_leaves
        );

        $casual_arr = array(
            'leave_type_id' => 1,
            'leave_type_name' => 'Casual',
            'total' => $total_no_of_casual_leaves,
            'taken' => $current_year_taken_c_l,
            'available' => $available_no_of_casual_leaves
        );

        $main_arr = array();
        array_push($main_arr, $annual_arr);
        array_push($main_arr, $casual_arr);

        //return response()->json($results);

        return (new BaseController)->sendResponse($main_arr, 'Leave Details');
    }

    public function MarkEmployeeAbsent(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj = new EmployeeAbsent();
        $obj->emp_id = $request->employee_id;
        $obj->from_date = $request->from_date;
        $obj->to_date = $request->to_date;
        $obj->save();

        return (new BaseController)->sendResponse($obj, 'Record Inserted');

    }

    public function GetRoutesList(Request $request)
    {
        $routes = Routes::
            with('emp_route')
            ->with('vehicle_type_rel')
            ->get();

        if(EMPTY($routes)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($routes, 'Routes List');
    }

    public function MarkEmployeeAvailability(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'date' => 'required',
            'session' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        //check if record find
        $record = EmployeeAvailability::where('emp_id', $request->employee_id)->where('date', $request->date)->first();

        if (EMPTY($record)){
            $obj = new EmployeeAvailability();
            $obj->emp_id = $request->employee_id;
            $obj->date = $request->date;
            $obj->session = $request->session;
            $obj->save();
        }else{
            $record->session = $request->session;
            $record->save();
        }

        return (new BaseController)->sendResponse(array(), 'Record Inserted');

    }

    public function GetLeaveListByStatus(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::where('status', $request->status )-> with('leave_type')
            ->get();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leaves List');
    }

    public function GetLeaveDetailsToView(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $leaves = Leave::where('id', $request->id )
            ->first();

        if(EMPTY($leaves)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($leaves, 'Leave Details');
    }

    public function UpdateLeaveStatus(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj_leave = Leave::where('id', $request->id)->first();

        if(EMPTY($obj_leave)){
            return (new BaseController)->sendError('Invalid Leave', ['error' => 'Invalid User']);
        }

        $obj_leave->status = $request->status;
        $obj_leave->save();

        return (new BaseController)->sendResponse($obj_leave, 'Leave Updated');

    }

    public function leaverequestinsert(Request $request){

        $employee=$request->input('employee');
        $fromdate=$request->input('fromdate');
        $todate=$request->input('todate');
        $half_short=$request->input('half_short');
        $reason=$request->input('reason');

        $request = new LeaveRequest();
        $request->emp_id=$employee;
        $request->from_date=$fromdate;
        $request->to_date=$todate;
        $request->leave_category=$half_short;
        $request->reason=$reason;
        $request->status= '1';
        $request->created_by=Auth::id();
        $request->updated_by = '0';
        $request->approve_status = '0';
        $request->request_approve_status = '0';
        $request->save();

        return (new BaseController)->sendResponse($request, 'Leave Request Details Successfully Insert');
    }

    public function leaverequest_list(Request $request){

        $employee = $request->get('employee');

        $query =  DB::table('leave_request')
        ->leftjoin('employees as emp', 'leave_request.emp_id', '=', 'emp.emp_id')
        ->leftjoin('departments', 'emp.emp_department', '=', 'departments.id')
        ->leftjoin('leaves', 'leave_request.id', '=', 'leaves.request_id')
        ->leftjoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
        ->select(
            'leave_request.*', 
            'emp.emp_name_with_initial as emp_name', 
            'departments.name as dep_name', 
            'leaves.leave_type as leave_type_id', 
            'leave_types.leave_type as leave_type_name', 
            'leaves.status as leave_status',
            'leaves.half_short as half_short'
        )
        ->where('leave_request.status', 1)
        ->where('leave_request.emp_id', $employee)
        ->get();

        $data = array(
            'leaverequests' => $query,
        );

        return (new BaseController)->sendResponse($data, 'leaverequests');

    }

    public function get_employee_monthlysummery(Request $request)
    {
        $selectedmonth = $request->input('selectedmonth');
        $emprecordid = $request->input('emprecordid');
        $empid = $request->input('empid');
        $emplocation = $request->input('emplocation');
           
        $monthworkingdaysdata=DB::table('employees')
                            ->leftJoin('job_categories','job_categories.id','employees.job_category_id')
                            ->select('employees.job_category_id','job_categories.emp_payroll_workdays')
                            ->where('employees.id',$emprecordid)
                            ->first();

        $monthworkingdays=$monthworkingdaysdata->emp_payroll_workdays;

        $work_days = (new \App\Attendance)->get_work_days($empid, $selectedmonth);
                                       
        $working_week_days_arr = (new \App\Attendance)->get_working_week_days($empid, $selectedmonth)['no_of_working_workdays'];
                                          
        $leave_days = (new \App\Attendance)->get_leave_days($empid, $selectedmonth);
                                           
        $no_pay_days = (new \App\Attendance)->get_no_pay_days($empid, $selectedmonth);
                         
                           
        $attendance_responseData= array(
            'workingdays'=>  $work_days,
            'absentdays'=>  ($monthworkingdays-$work_days),
            'working_week_days_arr'=>  $working_week_days_arr,
            'leave_days'=>  $leave_days,
            'no_pay_days'=>  $no_pay_days,
        );

        // payroll part--------------------------------------------------------------------------------------------------------------------------------
        
        $payment_period = DB::table('employee_payslips')
        ->leftjoin('payroll_profiles','payroll_profiles.id','employee_payslips.payroll_profile_id')
        ->select('employee_payslips.id','employee_payslips.payment_period_id','employee_payslips.payment_period_fr','employee_payslips.payment_period_to')
        ->where('employee_payslips.payment_period_fr', 'LIKE', $selectedmonth.'-%')
        ->where('payroll_profiles.emp_id', $emprecordid)
        ->where('employee_payslips.payslip_cancel', '0')
        ->orderBy('employee_payslips.id', 'desc')  // Order by payment_period_fr in descending order
        ->first();
        
        $payment_period_id=$payment_period->payment_period_id;
        $payment_period_fr=$payment_period->payment_period_fr;
        $payment_period_to=$payment_period->payment_period_to;

        //branches.location as location - branches.region as location
        //INNER JOIN branches ON employees.emp_location - INNER JOIN regions AS branches ON employees.region_id

            $sqlslip="SELECT 
                            drv_emp.emp_payslip_id, 
                            drv_emp.emp_epfno, 
                            drv_emp.emp_first_name, 
                            drv_emp.location, 
                            drv_emp.payslip_held, 
                            drv_emp.payslip_approved, 
                            drv_info.fig_group_title, 
                            drv_info.fig_group, 
                            drv_info.fig_value AS fig_value, 
                            drv_info.epf_payable AS epf_payable, 
                            drv_info.remuneration_pssc, 
                            drv_info.remuneration_tcsc 
                        FROM 
                            (SELECT employee_payslips.id AS emp_payslip_id, 
                            employees.emp_id AS emp_epfno, 
                            employees.emp_name_with_initial AS emp_first_name, 
                            companies.name AS location, 
                            employee_payslips.payslip_held, 
                            employee_payslips.payslip_approved 
                        FROM `employee_payslips` 
                        INNER JOIN payroll_profiles ON employee_payslips.payroll_profile_id=payroll_profiles.id 
                        INNER JOIN employees ON payroll_profiles.emp_id=employees.id 
                        INNER JOIN companies ON employees.emp_company=companies.id 
                            WHERE employee_payslips.payment_period_id=? AND employees.emp_company=? AND employees.id=?  AND employee_payslips.payslip_cancel=0) AS drv_emp 
                        INNER JOIN 
                        (SELECT `id` AS fig_id, `employee_payslip_id`, `fig_group_title`, `fig_group`, `epf_payable`, remuneration_payslip_spec_code AS remuneration_pssc, remuneration_taxcalc_spec_code AS remuneration_tcsc, `fig_value` AS fig_value 
                        FROM employee_salary_payments 
                        WHERE `payment_period_id`=?) AS drv_info ON drv_emp.emp_payslip_id=drv_info.employee_payslip_id ORDER BY drv_info.fig_id";
       
        
        $employee = DB::select($sqlslip, [$payment_period_id, $emplocation, $emprecordid, $payment_period_id]);
    

        $sect_name = $request->rpt_dept_name;
		$paymonth_name = Carbon::createFromFormat('Y-m-d', $payment_period_fr)->format('F Y');//format('F');
		/*
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Before Nopay', 'Arrears', 'Total for Tax', 'Attendance', 'Transport', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'EPF-8', 'Salary Advance', 'Telephone', 'IOU', 'Funeral Fund', 'Other Deductions', 'PAYE', 'Loans', 'Total Deductions', 'Balance Pay');
		*/
		$emp_array[] = array('EPF NO', 'Employee Name', 'Basic', 'BRA I', 'BRA II', 'No-pay', 'Total Salary Before Nopay', 'Arrears', 'Weekly Attendance', 'Incentive', 'Director Incentive', 'Other Addition', 'Salary Arrears', 'Normal', 'Double', 'Total Earned', 'Total for Tax', 'EPF-8', 'Salary Advance', 'Loans', 'IOU', 'Funeral Fund', 'PAYE', 'Other Deductions', 'Total Deductions', 'Balance Pay', 'EPF-12', 'ETF-3');
		/*
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'tot_fortax'=>0, 'ATTBONUS'=>0, 'add_transport'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'ded_tp'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'ded_other'=>0, 'PAYE'=>0, 'LOAN'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'OTHER_REM'=>0);
		*/
		$sum_array = array('emp_epfno'=>'', 'emp_first_name'=>'', 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
		
		$cnt = 1;
		$act_payslip_id = '';
		$net_payslip_fig_value = 0;
		$emp_fig_totearn = 0;
		$emp_fig_otherearn = 0; //other-additions
		$emp_fig_totlost = 0;
		$emp_fig_otherlost = 0; //other-deductions
		$emp_fig_tottax = 0;
		
		$rem_tot_bnp = 0;
		$rem_tot_fortax = 0;
		$rem_tot_earn = 0;
		$rem_tot_ded = 0;
		$rem_net_sal = 0;
		$rem_ded_other = 0;
		
		//2023-11-07
		//keys-selected-to-calc-paye-updated-from-remuneration-taxation
		
        $conf_tl = DB::table('remuneration_taxations')
        ->where(['fig_calc_opt' => 'FIGPAYE', 'optspec_cancel' => 0])
        ->pluck('taxcalc_spec_code')
        ->toArray();
//var_dump($conf_tl);
		//return response()->json($conf_tl);
		//-2023-11-07
		
		foreach($employee as $r){
			if($act_payslip_id!=$r->emp_payslip_id){
				$cnt++;
				$act_payslip_id=$r->emp_payslip_id;
				$net_payslip_fig_value = 0;
				$emp_fig_totearn = 0; $emp_fig_otherearn = 0;
				$emp_fig_totlost = 0; $emp_fig_otherlost = 0;
				$emp_fig_tottax = 0;
			}
			if(!isset($emp_array[$cnt-1])){
				$emp_array[] = array('emp_epfno'=>$r->emp_epfno, 'emp_first_name'=>$r->emp_first_name, 'BASIC'=>0, 'BRA_I'=>'0', 'add_bra2'=>'0', 'NOPAY'=>0, 'tot_bnp'=>0, 'sal_arrears1'=>0, 'ATTBONUS_W'=>0, 'INCNTV_EMP'=>0, 'INCNTV_DIR'=>0, 'add_other'=>0, 'sal_arrears2'=>0, 'OTHRS1'=>0, 'OTHRS2'=>0, 'tot_earn'=>0, 'tot_fortax'=>0, 'EPF8'=>0, 'sal_adv'=>0, 'LOAN'=>0, 'ded_IOU'=>0, 'ded_fund_1'=>0, 'PAYE'=>0, 'ded_other'=>0, 'tot_ded'=>0, 'NETSAL'=>0, 'EPF12'=>0, 'ETF3'=>0, 'OTHER_REM'=>0);
				
				$rem_tot_bnp = 0;
				$rem_tot_fortax = 0;
				$rem_tot_earn = 0;
				$rem_tot_ded = 0;
				$rem_net_sal = 0;
				$rem_ded_other = 0;
			}
			
			
			$fig_key = isset($emp_array[$cnt-1][$r->fig_group_title])?$r->fig_group_title:$r->remuneration_pssc;
			
			if(isset($emp_array[$cnt-1][$fig_key])){
				$fig_group_val=$emp_array[$cnt-1][$fig_key];
				
				if($fig_key!='OTHER_REM'){//prevent-other-rem-column-values-being-show-up-in-excel
					$emp_array[$cnt-1][$fig_key]=(abs($r->fig_value)+$fig_group_val);//number_format((float)(abs($r->fig_value)+$fig_group_val), 2, '.', '');
					$sum_array[$fig_key]+=abs($r->fig_value);
				}
				
				if(!(($r->fig_group_title=='EPF12') || ($r->fig_group_title=='ETF3'))){
					$net_payslip_fig_value+=$r->fig_value;
					$emp_array[$cnt-1]['NETSAL']=$net_payslip_fig_value;//number_format((float)$net_payslip_fig_value, 2, '.', '');
					
					$reg_net_sal=$sum_array['NETSAL']-$rem_net_sal;
					$sum_array['NETSAL']=($reg_net_sal+$net_payslip_fig_value);
					$rem_net_sal = $net_payslip_fig_value;
					
					/*
					if(($r->epf_payable==1)||($fig_key=='NOPAY')){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					*/
					if(in_array($r->remuneration_tcsc, $conf_tl)){
						$emp_fig_tottax += $r->fig_value;
						$emp_array[$cnt-1]['tot_fortax']=$emp_fig_tottax;//number_format((float)$emp_fig_tottax, 2, '.', '');
						
						$reg_tot_fortax=$sum_array['tot_fortax']-$rem_tot_fortax;
						$sum_array['tot_fortax']=($reg_tot_fortax+$emp_fig_tottax);
						$rem_tot_fortax = $emp_fig_tottax;
					}
					
					$fig_otherrem = ($fig_key=='OTHER_REM')?1:0;
					
					//if(($r->fig_value>=0)||($fig_key!='EPF8'))
					if((($r->fig_value>=0)&&($fig_key!='EPF8'))||($fig_key=='NOPAY')){
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
					}
					
					if($r->fig_value>=0){
						/*
						$emp_fig_totearn += $r->fig_value;
						$emp_array[$cnt-1]['tot_earn']=$emp_fig_totearn;//number_format((float)$emp_fig_totearn, 2, '.', '');
						
						$reg_tot_earn=$sum_array['tot_earn']-$rem_tot_earn;
						$sum_array['tot_earn']=($reg_tot_earn+$emp_fig_totearn);
						$rem_tot_earn = $emp_fig_totearn;
						*/
						$emp_fig_otherearn += ($r->fig_value*$fig_otherrem);
						$emp_array[$cnt-1]['add_other']=$emp_fig_otherearn;//number_format((float)$emp_fig_otherearn, 2, '.', '');
						
						
					}else{
						if($fig_key!='NOPAY'){
							$emp_fig_totlost += $r->fig_value;
							$emp_array[$cnt-1]['tot_ded']=abs($emp_fig_totlost);//number_format((float)abs($emp_fig_totlost), 2, '.', '');
							
							$reg_tot_ded=$sum_array['tot_ded']-$rem_tot_ded;
							$sum_array['tot_ded']=($reg_tot_ded+abs($emp_fig_totlost));
							$rem_tot_ded = abs($emp_fig_totlost);
						}
						$emp_fig_otherlost += (abs($r->fig_value)*$fig_otherrem);
						$emp_array[$cnt-1]['ded_other']=$emp_fig_otherlost;//number_format((float)$emp_fig_otherlost, 2, '.', '');
						
						$reg_ded_other=$sum_array['ded_other']-$rem_ded_other;
						$sum_array['ded_other']=($reg_ded_other+$emp_fig_otherlost);
						$rem_ded_other=$emp_fig_otherlost;
					}

				}
				
				if(($fig_key=='BASIC')||($fig_key=='BRA_I')||($fig_key=='add_bra2')){
					//if($emp_array[$cnt-1]['tot_bnp']==0){
						$emp_tot_bnp=($emp_array[$cnt-1]['BASIC']+$emp_array[$cnt-1]['BRA_I']+$emp_array[$cnt-1]['add_bra2']);
						$emp_array[$cnt-1]['tot_bnp']=$emp_tot_bnp;//number_format((float)$emp_tot_bnp, 2, '.', '');
						
						$reg_tot_bnp=$sum_array['tot_bnp']-$rem_tot_bnp;
						$sum_array['tot_bnp']=($reg_tot_bnp+$emp_tot_bnp);
						$rem_tot_bnp = $emp_tot_bnp;
					//}
				}
			}
		}
        
        $data = array(
            'result' => $attendance_responseData,
            'salaryresult'=>$sum_array
        );

        return (new BaseController)->sendResponse($data, 'result','salaryresult');
    }
}
