<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasShiftPermissions = $user->can('shift-list') || 
                         $user->can('work-shift-list') || 
                         $user->can('additional-shift-list') ||
                         $user->can('employee-shift-allocation-list') ||
                         $user->can('employee-ot-allocation-list') ||
                         $user->can('employee-shift-extend-list');
  @endphp

  @if($hasShiftPermissions)
      <div class="dropdown">
        @if($user->can('shift-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('Shift') }}" id="shift_link">Employee Shifts <span class="caret"></span></a>
        @endif
        @if($user->can('work-shift-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('ShiftType') }}" id="work_shift_link">Work Shifts <span class="caret"></span></a>
        @endif
        @if($user->can('additional-shift-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('AdditionalShift.index') }}" id="additional_shift_link">Additional Shifts <span class="caret"></span></a>
        @endif

        @if($user->can('employee-shift-allocation-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('employeeshift') }}" id="employeeshift_link">Employee Night Shift Assign <span class="caret"></span></a>
        @endif

        @if($user->can('employee-ot-allocation-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('employeeot') }}" id="employeeot_link">Employee OT Allocation Assign <span class="caret"></span></a>
        @endif

        @if($user->can('employee-shift-extend-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('empshiftextend') }}" id="employeeshift_extend_link">Employee Shift Extend Assign <span class="caret"></span></a>
        @endif
      </div>
  @endif

  @php
    $hasTransportPermissions = $user->can('transport-route-list') ||
                             $user->can('transport-vehicle-list') ||
                             $user->can('transport-allocation-list');
  @endphp

  @if($hasTransportPermissions)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="transport_link">
      Transport Management <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('transport-route-list'))
            <li><a class="dropdown-item" href="{{ route('TransportRoute')}}">Transport Route</a></li>
          @endif
          @if($user->can('transport-vehicle-list'))
            <li><a class="dropdown-item" href="{{ route('TransportVehicle')}}">Transport Vehicle</a></li>
          @endif
          @if($user->can('transport-allocation-list'))
            <li><a class="dropdown-item" href="{{ route('TransportAllocation')}}">Transport Allocation</a></li>
          @endif
        </ul>
  </div>
  @endif
</div>