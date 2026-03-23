<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use App\Holiday;
use App\Leave;
use Carbon\Carbon;

class IncompleteAttendanceController extends Controller
{
     public function incomplete_attendances()
    {
        $user = Auth::user();
        $permission = $user->can('incomplete-attendance-list');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.incomplete_attendances');
    }

       
    public function get_incomplete_attendance_by_employee_data(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('incomplete-attendance-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = Request('department');
        $employee = Request('employee');
        $location = Request('location');
        $from_date = Request('from_date');
        $to_date = Request('to_date');

         $dept_sql = "SELECT * FROM departments";

        if ($department != '') {
            $dept_sql .= ' WHERE id = "' . $department . '" ';
        }

        if ($location != '') {
            $dept_sql .= 'AND company_id = "' . $location . '" ';
        }

        $departments = DB::select($dept_sql);
        

        $data_arr = array();
        $not_att_count = 0;

        foreach ($departments as $department_) {

            $query = DB::table('employees')
                ->select(
                    'employees.emp_id',
                    'employees.emp_name_with_initial',
                    'employees.emp_etfno',
                    'branches.location as b_location',
                    'departments.name as dept_name',
                    'departments.id as dept_id'
                )
                ->leftJoin('branches', 'employees.emp_location', '=', 'branches.id')
                ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
                ->where('employees.deleted', 0)
                ->where('employees.status', 1)
                ->where('employees.is_resigned', 0)
                ->where('departments.id', $department_->id);

            if ($employee != '') {
                $query->where('employees.emp_id', $employee);
            }

            $employees = $query->orderBy('employees.emp_id', 'asc')->get();

            foreach ($employees as $record) {

                 
                //dates of the month between from and to date
                $period = CarbonPeriod::create($from_date, $to_date);

                foreach ($period as $date) {
                    $f_date = $date->format('Y-m-d');

                  
                    //check this is not a holiday
                    $holiday_check = Holiday::where('date', $f_date)->first();

                    if (empty($holiday_check)) {

                        //check leaves from_date to date and emp_id is not a leave
                        $leave_check = Leave::where('emp_id', $record->emp_id)
                            ->where('leave_from', '<=', $f_date)
                            ->where('leave_to', '>=', $f_date)->first();

                        if (empty($leave_check)) {

                            $sql = "SELECT * FROM attendances 
                                            WHERE uid = '" . $record->emp_id . "' 
                                            AND deleted_at IS NULL
                                            AND date LIKE '" . $f_date . "%'
                                            ORDER BY timestamp ASC";

                            $attendances = DB::select($sql);

                           
                            
                            if (!empty($attendances) && count($attendances) == 1) {
                                $single_attendance = $attendances[0];

                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['emp_id'] = $record->emp_id;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['emp_name_with_initial'] = $record->emp_name_with_initial;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['etf_no'] = $record->emp_etfno;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['b_location'] = $record->b_location;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['dept_name'] = $record->dept_name;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['dept_id'] = $record->dept_id;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['date'] = $f_date;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['timestamp'] = $single_attendance->timestamp;
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['lasttimestamp'] = '-';
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['workhours'] = '-';
                                $data_arr[$department_->id][$record->emp_id][$not_att_count]['location'] = $record->b_location;

                                $not_att_count++;
                            }

                        }// leave check if

                    }//holiday if end

                }// period loop

            }//employees loop


        }//departments loop



        $department_id = 0;

        $html = '<div class="row mb-1"> 
                    <div class="col-md-4"> 
                    </div>
                    
                    <div class="col-md-4"> 
                    </div>
                     
                </div>';
        $html .= '<div class="mb-3 d-flex justify-content-end">
                <button id="export_pdf" class="btn btn-outline-primary btn-sm mr-2"><i class="fa fa-file-pdf"></i> Export PDF</button>
                <button id="export_excel" class="btn btn-outline-success btn-sm"><i class="fa fa-file-excel"></i> Export Excel</button>
                </div>';
        $html .= '<table class="table table-sm table-hover" id="attendance_report_table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th> </th>';
        $html .= '<th>EMP ID</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Department</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Check In Time</th>';
        $html .= '<th>Check Out Time</th>';
        $html .= '<th>Location</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($data_arr as $dept_key => $department_data) {

            //if department_id is not equal to the previous department_id
            if ($department_id != $dept_key) {
                $department_id = $dept_key;
                $department_name = Department::query()->where('id', $department_id)->first()->name;
                $html .= '<tr>';
                $html .= '<td colspan="8" style="background-color: #f5f5f5;"> <strong> ' . $department_name . '</strong> </td>';
                $html .= '</tr>';
            }

            foreach ($department_data as $emp_data) {

                foreach ($emp_data as $attendance) {

                    $tr = '<tr>';

                    $html .= $tr;
                    $html .= '<td> 
                                <input type="checkbox" class="checkbox_attendance" name="checkbox[]" value="' . $attendance['etf_no'] . '"
                                    data-etf_no="' . $attendance['etf_no'] . '" 
                                    data-date = "' . $attendance['date'] . '" 
                                    data-empid = "' . $attendance['emp_id'] . '"
                                 />
                                </td>';

                    $first_time = date('H:i', strtotime($attendance['timestamp']));
                    $last_time = date('H:i', strtotime($attendance['lasttimestamp']));

                    $html .= '<td>' . $attendance['emp_id'] . '</td>';
                    $html .= '<td>' . $attendance['emp_name_with_initial'] . '</td>';
                    $html .= '<td>' . $attendance['dept_name'] . '</td>';
                    $html .= '<td>' . $attendance['date'] . '</td>';
                    $html .= '<td><input type="datetime-local" class="form-control form-control-sm time_in" 
                                    data-timestamp="' . ($attendance['timestamp'] ?? '') . '" 
                                    value="' . $attendance['timestamp'] . '" 
                                    placeholder="YYYY-MM-DD HH:MM" /></td>';
                    $html .= '<td><input type="datetime-local" class="form-control form-control-sm time_out" 
                                    data-timestamp="' . ($attendance['lasttimestamp'] ?? '') . '" 
                                    value="' . $attendance['lasttimestamp'] . '" 
                                    placeholder="YYYY-MM-DD HH:MM" /></td>';
                    $html .= '<td>' . $attendance['location'] . '</td>';
                    $html .= '</tr>';
                    $department_id = $attendance['dept_id'];

                }
            }
        }

        $html .= '</tbody>';
        $html .= '</table>

                <div class="row mt-12 justify-content-end"> 
                    <div class="col-md-auto"> 
                        <button type="button" class="btn btn-primary btn-sm" id="btn_mark_as_no_pay">Mark as NO Pay Leave</button>
                    </div> 
                    <div class="col-md-auto"> 
                        <button type="button" class="btn btn-success btn-sm" id="btn_updatesttendace">Update Attendance</button>
                    </div>  
                </div>';


        //return json response
        echo $html;

    }

    public function update_attendace(Request $request)
    {
            $records = $request->input('updatedrecords');
            
           
        foreach ($records as $record) {
            $empId = $record['emp_id'];
            $date = Carbon::parse($record['date'])->format('Y-m-d 00:00:00');

             $checkIn = Carbon::parse($record['timestamp'])->format('Y-m-d H:i:s');
             $checkOut = Carbon::parse($record['lasttimestamp'])->format('Y-m-d H:i:s');


            $timein_exist = DB::table('attendances')
            ->where('emp_id', $empId)
            ->where('date', $date)
            ->where('timestamp', $checkIn)
            ->whereNull('deleted_at')
            ->exists();

            if(!$timein_exist){
              DB::table('attendances')->insert([
                    'emp_id' => $empId,
                    'uid' => $empId,
                    'state' => 1,
                    'timestamp' => $checkIn,
                    'date' => $date,
                    'approved' => 0,
                    'type' => 255,
                    'location' => 1
                ]);

            }

            $timeout_exist = DB::table('attendances')
            ->where('emp_id', $empId)
            ->where('date', $date)
            ->where('timestamp', $checkOut)
            ->whereNull('deleted_at')
            ->exists();

            if(!$timeout_exist){
                    DB::table('attendances')->insert([
                    'emp_id' => $empId,
                    'uid' => $empId,
                    'state' => 1,
                    'timestamp' => $checkOut,
                    'date' => $date,
                    'approved' => 0,
                    'type' => 255,
                    'location' => 1
                ]);
            }

        }
             return response()->json(['success' => 'Attendance Updated successfully.']);
        
    }
}
