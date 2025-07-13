
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('shift-list')
  || auth()->user()->can('work-shift-list')
  || auth()->user()->can('additional-shift-list'))
      <div class="dropdown">
        @can('shift-list')
        <a role="button" class="btn navbtncolor" href="{{ route('Shift') }}" id="shift_link">Employee Shifts <span class="caret"></span></a>
        @endcan
        @can('work-shift-list')
        <a role="button" class="btn navbtncolor" href="{{ route('ShiftType') }}" id="work_shift_link">Work Shifts <span class="caret"></span></a>
        @endcan
        @can('additional-shift-list')
        <a role="button" class="btn navbtncolor" href="{{ route('AdditionalShift.index') }}" id="additional_shift_link">Additional Shifts <span class="caret"></span></a>
        @endcan

        @can('employee-shift-allocation-list')
        <a role="button" class="btn navbtncolor" href="{{ route('employeeshift') }}" id="employeeshift_link">Employee Night Shift Assign <span class="caret"></span></a>
        @endcan

        @can('employee-ot-allocation-list')
        <a role="button" class="btn navbtncolor" href="{{ route('employeeot') }}" id="employeeot_link">Employee OT Allocation Assign <span class="caret"></span></a>
        @endcan

        @can('employee-shift-extend-list')
        <a role="button" class="btn navbtncolor" href="{{ route('empshiftextend') }}" id="employeeshift_extend_link">Employee Shift Extend Assign <span class="caret"></span></a>
        @endcan

      </div>
  @endif

  @if(auth()->user()->can('transport-route-list')
  || auth()->user()->can('transport-vehicle-list')
  || auth()->user()->can('transport-allocation-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="transport_link">
      Transport Management <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('transport-route-list')
            <li><a class="dropdown-item" href="{{ route('TransportRoute')}}">Transport Route</a></li>
            @endcan
          @can('transport-vehicle-list')
            <li><a class="dropdown-item" href="{{ route('TransportVehicle')}}">Transport Vehicle</a></li>
            @endcan
          @can('transport-allocation-list')
            <li><a class="dropdown-item" href="{{ route('TransportAllocation')}}">Transport Allocation</a></li>
            @endcan
        </ul>
  </div>
  @endif

</div>


