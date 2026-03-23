<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @php
    $user = auth()->user();
    $hasAttendancePermissions = $user->can('attendance-sync') || $user->can('attendance-incomplete-data-list') || 
                               $user->can('attendance-list') || $user->can('attendance-create') || 
                               $user->can('attendance-edit') || $user->can('attendance-delete') || 
                               $user->can('attendance-approve') || $user->can('late-attendance-create') || 
                               $user->can('late-attendance-approve') || $user->can('late-attendance-list') || 
                               $user->can('attendance-incomplete-data-list') || $user->can('ot-approve') || 
                               $user->can('ot-list') || $user->can('finger-print-device-list') || 
                               $user->can('finger-print-user-list') || $user->can('attendance-device-clear');
  @endphp

  @if($hasAttendancePermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="attendantmaster">
      Attendance Information<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('finger-print-device-list'))
          <li><a class="dropdown-item" href="{{ route('FingerprintDevice')}}">Fingerprint Device</a></li>
          @endif
          @if($user->can('finger-print-user-list'))
          <li><a class="dropdown-item" href="{{ route('FingerprintUser')}}">Fingerprint User</a></li>
          @endif
          @if($user->can('attendance-device-clear-list'))
          <li><a class="dropdown-item" href="{{ route('AttendanceDeviceClear')}}">Attendance Device Clear</a></li>
          @endif

          @if($user->can('attendance-sync'))
            <li><a class="dropdown-item" href="{{ route('Attendance')}}">Attendance Sync</a></li>
          @endif
          @if($user->can('attendance-create'))
            <li><a class="dropdown-item" href="{{ route('AttendanceEdit')}}">Attendance Add</a></li>
          @endif
          @if($user->can('attendance-edit'))
            <li><a class="dropdown-item" href="{{ route('AttendanceEditBulk')}}">Attendance Edit</a></li>
          @endif
          @if($user->can('late-attendance-create'))
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time')}}">Late Attendance Mark</a></li>
          @endif
          @if($user->can('late-attendance-approve'))
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time_approve')}}">Late Attendance Approve</a></li>
          @endif
          @if($user->can('late-attendance-list'))
            <li><a class="dropdown-item" href="{{ route('late_attendances_all')}}">Late Attendances</a></li>
          @endif
          @if($user->can('Late-minites-manual-mark-list'))
          <li><a class="dropdown-item" href="{{ route('lateminitesmanualmark')}}">Late Attendance Auto Mark</a></li>
          @endif
          @if($user->can('attendance-incomplete-data-list'))
            <li><a class="dropdown-item" href="{{ route('incomplete_attendances')}}">Incomplete Attendances</a></li>
          @endif
          @if($user->can('Nopay-attendance-list'))
          <li><a class="dropdown-item" href="{{ route('nopay_attendances')}}">Nopay Attendances</a></li>
          @endif
          @if($user->can('ot-approve'))
            <li><a class="dropdown-item" href="{{ route('ot_approve')}}">OT Approve</a></li>
          @endif
          @if($user->can('ot-list'))
            <li><a class="dropdown-item" href="{{ route('ot_approved')}}">Approved OT</a></li>
          @endif
          @if($user->can('attendance-approve'))
            <li><a class="dropdown-item" href="{{ route('AttendanceApprovel')}}">Attendance Approval</a></li>
          @endif
          @if($user->can('Lateminites-Approvel-list'))
          <li><a class="dropdown-item" href="{{ route('lateminitesapprovel')}}">Late Minites Approval</a></li>
          @endif
          @if($user->can('MealAllowanceApprove-list'))
          <li><a class="dropdown-item" href="{{ route('mealallowanceapproval')}}">Salary Adjustments Approval</a></li>
          @endif
          @if($user->can('Holiday-DeductionApprove-list'))
          <li><a class="dropdown-item" href="{{ route('holidaydeductionapproval')}}">Leave Deduction Approval</a></li>
          @endif
          @if($user->can('Absent-Nopay-list'))
          <li><a class="dropdown-item" href="{{ route('absentnopay')}}">Absent Noapy Apply</a></li>
          @endif
        </ul>
  </div>
  @endif

  @php
    $hasLeavePermissions = $user->can('leave-list') || $user->can('leave-type-list') || 
                          $user->can('leave-approve') || $user->can('holiday-list') || 
                          $user->can('IgnoreDay-list') || $user->can('Coverup-list') || 
                          $user->can('Holiday-Deduction-list');
  @endphp

  @if($hasLeavePermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="leavemaster">
        Leave Information <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('leave-list'))
            <li><a class="dropdown-item" href="{{ route('LeaveApply')}}">Leave Apply</a></li>
          @endif
          @if($user->can('leave-type-list'))
            <li><a class="dropdown-item" href="{{ route('LeaveType')}}">Leave Type</a></li>
          @endif
          @if($user->can('leave-approve'))
            <li><a class="dropdown-item" href="{{ route('LeaveApprovel')}}">Leave Approvals</a></li>
          @endif
          @if($user->can('holiday-list'))
            <li><a class="dropdown-item" href="{{ route('Holiday')}}">Holiday</a></li>
          @endif
          @if($user->can('IgnoreDay-list'))
            <li><a class="dropdown-item" href="{{ route('IgnoreDay')}}">Ignore Days</a></li>
          @endif
          @if($user->can('Coverup-list'))
            <li><a class="dropdown-item" href="{{ route('Coverup')}}">CoverUp Details</a></li>
          @endif
          @if($user->can('Coverup-Approvel'))
          <li><a class="dropdown-item" href="{{ route('coverupnopay')}}">CoverUp Nopay Approvel</a></li>
          @endif
          @if($user->can('Holiday-Deduction-list'))
            <li><a class="dropdown-item" href="{{ route('HolidayDeduction')}}">Holiday Deduction</a></li>
          @endif
        </ul>
  </div>
  @endif

  @php
    $hasJobPermissions = $user->can('Job-Location-list') || 
                        $user->can('Job-Allocation-list') || 
                        $user->can('Job-Attendance-list');
  @endphp

  @if($hasJobPermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="jobmanegment">
      Job Management <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('Job-Location-list'))
            <li><a class="dropdown-item" href="{{ route('joblocations')}}">Job Location</a></li>
          @endif
          @if($user->can('Job-Allocation-list'))
            <li><a class="dropdown-item" href="{{ route('joballocation')}}">Job Allocation</a></li>
          @endif
          @if($user->can('Job-Attendance-list'))
            <li><a class="dropdown-item" href="{{ route('jobattendance')}}">Attendance</a></li>
          @endif
        </ul>
  </div>
  @endif

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="gatepassmanagement">
      Gate pass Management <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('gatepass')}}">Gate pass</a></li>
          <li><a class="dropdown-item" href="{{ route('gatepassapprove')}}">Gate pass Approve</a></li>
        </ul>
  </div>
  
  @if($user->can('daily-summary-list'))
  <a role="button" class="btn navbtncolor" href="{{ route('dailyhrsummary') }}" id="hrsummary">Daily HR Summary<span class="caret"></span></a>
  @endif
</div>