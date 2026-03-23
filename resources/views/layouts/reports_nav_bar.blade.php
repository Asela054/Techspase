<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasAttendanceReportPermissions = $user->can('attendance-report') ||
                                    $user->can('late-attendance-report') ||
                                    $user->can('leave-report') ||
                                    $user->can('leave-balance-report') ||
                                    $user->can('ot-report') ||
                                    $user->can('no-pay-report') ||
                                    $user->can('employee-absent-report') ||
                                    $user->can('Leave-type-report') ||
                                    $user->can('Absent-3Days-report') ||
                                    $user->can('employee-shift-allocation-report') ||
                                    $user->can('employee-shift-extend-report');
  @endphp

  @if($hasAttendanceReportPermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="employeereportmaster">
        Attendance & Leave Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('attendance-report'))
            <li><a class="dropdown-item" href="{{ route('attendetreportbyemployee')}}">Attendance Report</a></li>
          @endif
          @if($user->can('late-attendance-report'))
            <li><a class="dropdown-item" href="{{ route('LateAttendance')}}">Late Attendance</a></li>
          @endif
          @if($user->can('leave-report'))
            <li><a class="dropdown-item" href="{{ route('leaveReport')}}">Leave Report</a></li>
          @endif
          @if($user->can('leave-balance-report'))
            <li><a class="dropdown-item" href="{{ route('LeaveBalance')}}">Leave Balance</a></li>
          @endif
          @if($user->can('ot-report'))
            <li><a class="dropdown-item" href="{{ route('ot_report')}}">O.T. Report</a></li>
          @endif
          @if($user->can('no-pay-report'))
            <li><a class="dropdown-item" href="{{ route('no_pay_report')}}">No Pay Report</a></li>
          @endif
          @if($user->can('employee-absent-report'))
            <li><a class="dropdown-item" id="absent_report_link" href="{{ route('employee_absent_report') }}">Employee Absent Report</a></li>
          @endif
          @if($user->can('Leave-type-report'))
            <li><a class="dropdown-item" href="{{ route('leavetypereport') }}">Leave Type Report</a></li>
          @endif
          @if($user->can('OT-60After-report'))
            <li><a class="dropdown-item" href="{{ route('hrotreport') }}">OT 60hr After Report</a></li>
          @endif
          @if($user->can('Absent-3Days-report'))
            <li><a class="dropdown-item" href="{{ route('absentdaysreport') }}">Absent more than 3days report</a></li>
          @endif
          @if($user->can('employee-shift-allocation-report'))
            <li><a class="dropdown-item" href="{{ route('nightshiftreport') }}">Employee Night Shift Report</a></li>
          @endif
          @if($user->can('employee-shift-extend-report'))
            <li><a class="dropdown-item" href="{{ route('shiftextendreport') }}">Employee Shift Extend Report</a></li>
          @endif
        </ul>
  </div>
  @endif

  @php
    $hasEmployeeDetailPermissions = $user->can('employee-report') || 
                                  $user->can('employee-bank-report') ||
                                  $user->can('employee-resign-report') ||
                                  $user->can('employee-recruitment-report') ||
                                  $user->can('Employee-Carder-Report');
  @endphp

  @if($hasEmployeeDetailPermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="employeedetailsreport">
        Employee Details Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('employee-report'))
            <li><a class="dropdown-item" href="{{ route('EmpoloyeeReport')}}">Employees Report</a></li>
          @endif
          @if($user->can('employee-bank-report'))
            <li><a class="dropdown-item" href="{{ route('empBankReport')}}">Employee Banks</a></li>
          @endif
          @if($user->can('employee-resign-report'))
            <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('employee_resign_report') }}">Employee Resign Report</a></li>
          @endif
          @if($user->can('employee-recruitment-report'))
            <li><a class="dropdown-item" href="{{ route('employee_recirument_report') }}">Employee Recruitment Report</a></li>
          @endif
          @if($user->can('attendance-report'))
            <li><a class="dropdown-item" href="{{ route('employeeattendancereport') }}">Employee Attendance Report</a></li>
          @endif
          @if($user->can('Employee-Carder-Report'))
            <li><a class="dropdown-item" href="{{ route('carderreport') }}">Employee Carder Report</a></li>
          @endif
        </ul>
  </div>
  @endif

  @php
    $hasDepartmentWisePermissions = $user->can('department-wise-ot-report') || 
                                  $user->can('department-wise-leave-report') || 
                                  $user->can('department-wise-attendance-report');
  @endphp

  @if($hasDepartmentWisePermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="departmentvisereport">
      Department-Wise Reports<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('department-wise-attendance-report'))
            <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('departmentwise_attendancereport') }}">Department-Wise Attendance Report</a></li>
          @endif
          @if($user->can('department-wise-ot-report'))
            <li><a class="dropdown-item" href="{{ route('departmentwise_otreport')}}"> Department-Wise O.T. Report</a></li>
          @endif
          @if($user->can('department-wise-leave-report'))
            <li><a class="dropdown-item" href="{{ route('departmentwise_leavereport')}}">Department-Wise Leave Report</a></li>
          @endif
          @if($user->can('department-wise-leave-report'))
            <li><a class="dropdown-item" href="{{ route('joballocationreport')}}">Job Allocation Report</a></li>
          @endif
        </ul>
  </div>
  @endif

  @if($user->can('employee-ot-allocation-report'))
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="otallocationreport">
      OT Allocation Reports<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" id="transport_report_link" href="{{ route('transportreport') }}">Transport Report</a></li>
          <li><a class="dropdown-item" id="meal_report_link" href="{{ route('mealreport') }}">Meal Report</a></li>
        </ul>
  </div>
  @endif
</div>