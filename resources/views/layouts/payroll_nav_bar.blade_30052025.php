
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('Facilities-list') 
            || auth()->user()->can('Payrollprofile-list')
            || auth()->user()->can('Loans-list') 
            || auth()->user()->can('Loans-Settlement-list')
            || auth()->user()->can('Salaryaddition-list') 
            || auth()->user()->can('Other-facilities-list')
            || auth()->user()->can('Salary-increment-list') 
            || auth()->user()->can('Work-summary-list')
            || auth()->user()->can('Salary-preperation-list') 
            || auth()->user()->can('Salary-schedule-list')
            || auth()->user()->can('Paysliplist-list'))

  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="policymanagement">
       Policy Management<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            @can('Facilities-list')
            <li><a class="dropdown-item" href="{{ url('RemunerationList') }}" id="facilities">Facilities</a></li>
            @endcan
            @can('Payrollprofile-list')
            <li><a class="dropdown-item" href="{{ url('PayrollProfileList') }}" id="payrollprofile">Payroll Profile</a></li>
            @endcan
            @can('Loans-list')
            <li><a class="dropdown-item" href="{{ url('EmployeeLoanList') }}" id="loans">Loans</a></li>
            @endcan
            @can('Loan Approve')
            <li><a class="dropdown-item" href="{{ url('EmployeeLoanAdmin') }}">Loan Approval</a></li>
            @endcan
            @can('Loans-Settlement-list')
            <li><a class="dropdown-item" href="{{ url('EmployeeLoanInstallmentList') }}" id="loanSettlement">Loan Settlement</a></li>
            @endcan
            @can('Salaryaddition-list')
            <li><a class="dropdown-item" href="{{ url('EmployeeTermPaymentList') }}" id="SalaryAdditions">Salary Additions</a></li>
            @endcan
            @can('Other-facilities-list')
            <li><a class="dropdown-item" href="{{ url('OtherFacilityPaymentList') }}" id="OtherFacilities">Other Facilities</a></li>
            @endcan
            @can('Salary-increment-list')
            <li><a class="dropdown-item" href="{{ url('SalaryIncrementList') }}" id="SalaryIncrements">Salary Increments</a></li>
            @endcan
            @can('Work-summary-list')
            <li> <a class="dropdown-item" href="{{ url('SalaryProcessSchedule') }}" id="SalaryIncrements">Salary Schedule</a></li>
            @endcan
            @can('Salary-preperation-list')
            <li><a class="dropdown-item" href="{{ url('EmployeeWorkSummary') }}" id="Worksummary">Work Summary</a></li>
            @endcan
            @can('Salary-schedule-list')
            <li><a class="dropdown-item" href="{{ url('EmployeePayslipList') }}" id="SalaryPreperation">Salary Preperation</a></li>
            @endcan
            @can('Paysliplist-list')
            <li><a class="dropdown-item" href="{{ url('PayslipRegistry') }}" id="PayslipList">Payslip List</a></li>
            @endcan
        </ul>
  </div>
  @endif

  @if(auth()->user()->can('Pay-Register-Report') 
  || auth()->user()->can('OT-Report')
  || auth()->user()->can('EPF-ETF-Report') 
  || auth()->user()->can('Salary-Sheet-report')
  || auth()->user()->can('Salary-sheet-bankslip-report') 
  || auth()->user()->can('Salary-sheet-heldpayment-report')
  || auth()->user()->can('Sixmonths-report') 
  || auth()->user()->can('Addition-report'))

  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="payrollreport">
      Reports <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            @can('Pay-Register-Report')
             <li><a class="dropdown-item" href="{{ url('ReportPayRegister') }}" id="payregister">Pay Register</a></li>
            @endcan
            @can('OT-Report')
             <li><a class="dropdown-item" href="{{ url('ReportEmpOvertime') }}" id="otreport">OT Report</a></li>
            @endcan
            @can('EPF-ETF-Report')
             <li><a class="dropdown-item" href="{{ url('ReportEpfEtf') }}" id="epfetf">EPF and ETF</a></li>
            @endcan
            <!--a class="dropdown-item" href="{{ url('ReportSignatureSheet') }}">Signature Sheet</a-->
            @can('Salary-Sheet-report')
             <li><a class="dropdown-item" href="{{ url('ReportSalarySheet') }}" id="salarysheet">Salary Sheet</a></li>
            @endcan
            @can('Salary-sheet-bankslip-report')
             <li><a class="dropdown-item" href="{{ url('ReportSalarySheetBankSlip') }}" id="salarysheetbank">Salary Sheet - Bank Slip</a></li>
            @endcan
            @can('Salary-sheet-heldpayment-report')
             <li><a class="dropdown-item" href="{{ url('ReportHeldSalaries') }}" id="salaryheld">Salary Sheet - Held Payments</a></li>
            @endcan
            @can('Sixmonths-report')
             <li><a class="dropdown-item" href="{{ url('ReportSixMonth') }}" id="sixmonth">Six Month Report</a></li>
            @endcan
            @can('Addition-report')
             <li><a class="dropdown-item" href="{{ url('ReportAddition') }}" id="additionreport">Additions Report</a></li>
            @endcan
        </ul>
  </div>
  @endif

  @if(auth()->user()->can('Employee-Salary-Payment-Statement') 
            || auth()->user()->can('Employee-Incentive-Statement')
            || auth()->user()->can('Bank-Advice-Statement') 
            || auth()->user()->can('Pay-Summary-Statement')
            || auth()->user()->can('Employee-Salary-Journal-Statement') 
            || auth()->user()->can('EPF-ETF-Journal-Statement'))


  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="payrollststement">
      Statements <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('Employee-Salary-Payment-Statement')
           <li><a class="dropdown-item" href="{{ url('EmpSalaryPayVoucher') }}">Employee Salary (Payment Voucher)</a></li>
          @endcan
          @can('Employee-Incentive-Statement')
           <li><a class="dropdown-item" href="{{ url('EmpIncentivePayVoucher') }}">Employee Incentive (Payment Voucher)</a></li>
          @endcan
          @can('Bank-Advice-Statement')
           <li><a class="dropdown-item" href="{{ url('ReportBankAdvice') }}">Bank Advice</a></li>
          @endcan
          @can('Pay-Summary-Statement')
           <li><a class="dropdown-item" href="{{ url('ReportPaySummary') }}">Pay Summary</a></li>
          @endcan
          @can('Employee-Salary-Journal-Statement')
           <li><a class="dropdown-item" href="{{ url('EmpSalaryJournalVoucher') }}">Employee Salary (Journal Voucher)</a></li>
          @endcan
          @can('EPF-ETF-Journal-Statement')
           <li><a class="dropdown-item" href="{{ url('EmpEpfEtfJournalVoucher') }}">EPF and ETF (Journal Voucher)</a></li>
          @endcan
        </ul>
  </div>
  @endif


</div>


