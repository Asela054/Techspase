<!DOCTYPE html>
<html lang="si">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <style>
    @font-face {
        font-family: 'NotoSansSinhala';
        src: url("{{ public_path('fonts/NotoSansSinhala-Regular.ttf') }}") format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    
    @font-face {
        font-family: 'NotoSansSinhala';
        src: url("{{ public_path('fonts/NotoSansSinhala-Bold.ttf') }}") format('truetype');
        font-weight: bold;
        font-style: normal;
    }

    @page { 
        size: 105mm 297mm;
        margin: 5mm 5mm 5mm 5mm;
        font-family: 'NotoSansSinhala', sans-serif;
        font-size:13px;
    }

    body {
        font-family: 'NotoSansSinhala', sans-serif;
        direction: ltr;
        text-align: left;
        unicode-bidi: embed;
    }
    
    table{
        width: 100%;
        border-collapse: separate;
        border-spacing: 0; 
        border: 1px solid #000;
        border-radius: 5px;
        overflow: hidden;
    }
    
    th, td {
        padding: 4px;
    }
    
    .bodytd{
        border: 1px solid;
    }
   
  </style>
</head>
<body>

    @php $check=0 @endphp
    
        @for ($slipcnt=0;$slipcnt < count($emp_array);$slipcnt++)
  
            @if(isset($emp_array[$slipcnt]))
            
            @php $row=$emp_array[$slipcnt] @endphp
            
            @php 
                $netbasicValue = ($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']) - $row['NOPAY'];
                $totalearnValue = $row['OTHRS1'] + $row['OTHRS2'] + $row['ATTBONUS_W'] + $row['INCNTV_EMP'];
                $totalearnValue += $row['ded_fund_1'];
                $totalearnValue += $row['ded_tp'];
                $totalearnValue += $row['add_other'];
                
                $netbasic = number_format((float)$netbasicValue, 2, '.', ',');
                $totalearn = number_format((float)$totalearnValue, 2, '.', ',');
                
                $grosspay = number_format((float)($netbasicValue + $totalearnValue), 2, '.', ',');
            @endphp
            
            @php $nopay_days=$row['NOPAYCNT'] @endphp
            @php $holiday_nopay_days=$row['holiday_nopay_days'] @endphp
            @php $tot_nopay_days=$nopay_days+$holiday_nopay_days @endphp
            @php $nopaystr=($tot_nopay_days==0)?'0.00':($row['NOPAY']/$tot_nopay_days)*$nopay_days @endphp
            
            @php
            $tot_workholidays = $row['sunday_work_days']+$row['poya_work_days']+$row['mercantile_work_days'];
            $avg_workhrs = ($tot_workholidays > 0)?round(($row['holiday_normal_ot_hrs']+$row['holiday_double_ot_hrs'])/$tot_workholidays, 2):0;
            
            $sunday_workhrs = round($avg_workhrs*$row['sunday_work_days'], 2);
            $poya_workhrs = round($avg_workhrs*$row['poya_work_days'], 2);
            $mercantile_workhrs = $row['holiday_double_ot_hrs'];
            $sunday_double_othrs = $row['sunday_double_rate_otwork_hrs'];
            $poya_normal_othrs = $row['poya_normal_rate_otwork_hrs'];
            $mercantile_triple_othrs = $row['OTAMT3'];
            
            $ot1val = 0;
            $ot2val = 0;
            $ot3val = $row['OTHRS3'];
            $ot4tl_tothrs = $sunday_workhrs+$poya_workhrs;
            $ot4tl_avgval = ($ot4tl_tothrs>0)?round($row['OTHRS4_TL']/$ot4tl_tothrs, 2):0;
            $ot4tl_sundayval = round($ot4tl_avgval*$sunday_workhrs, 2);
            $ot4tl_poyaval_original = round($ot4tl_avgval*$poya_workhrs, 2);
            $ot5tl_mercantileval_original = $row['OTHRS5_TL'];
            $ot6tl_poya_normal_otval = $row['OTHRS6_TL'];
            $ot7tl_sunday_double_otval = $row['OTHRS7_TL'];
            $ot1val = $row['OTHRS1']-($row['OTHRS4_TL']+$row['OTHRS6_TL']);
            $ot2val = $row['OTHRS2']-($row['OTHRS5_TL']+$row['OTHRS7_TL']);
            
            $holiday_nopayval=($tot_nopay_days==0)?0:round(($row['NOPAY']/$tot_nopay_days)*$holiday_nopay_days, 2);
            $ot4tl_poya_ot5tl_mercantile_payval = ($ot4tl_poyaval_original+$ot5tl_mercantileval_original)-$holiday_nopayval;
            
            @endphp
            <table id="maintable">
                <tbody>
                    <tr>
                        <td colspan="3"><strong style="font-size: 14px;">{{ $company_name }}</strong></td>
                        <td colspan="3" style="text-align:right;font-family: 'NotoSansSinhala', sans-serif;"><strong>{{$paymonth_name}} ඔබට සුභ දවසක් වේවා!</strong></td>
                    </tr>
                    <tr >
                        <td  style="border-top: none; border-right:none;" colspan="3" ><b>{{ $company_addr }}</b></td>
                        <td  style="border-top: none; border-right:none;text-align:right;" colspan="3" ><b></b></td>
                    </tr>
                    <tr>
                        <td class="bodytd" colspan="2" style="border-left: none;border-right: none;  border-bottom:none;">
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td style="border:none;">NAME</td>
                                    <td style="border-right:none;">: &nbsp; {{ $row['emp_first_name'] }}</td>
                                </tr>
                                <tr>
                                    <td>NIC NO</td>
                                    <td>: &nbsp;{{ $row['emp_national_id'] }}</td>
                                </tr>
                                <tr>
                                    <td>EPF NO </td>
                                    <td>: &nbsp;{{ $row['emp_epfno'] }}</td>
                                </tr>
                            </table>
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td style=" border-top: 1px solid black;">BASIC SALARY </td>
                                    <td style="text-align:right; border-top: 1px solid black;" >{{ number_format((float)($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']), 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>NO PAY </td>
                                    <td style="text-align:right;">{{ number_format((float)$nopaystr, 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>NET BASIC</td>
                                    <td style="text-align:right;  border-top: 1px solid black;">{{ $netbasic }}</td>
                                </tr>
                                <tr>
                                    <td>RECEIVABLES</td>
                                    <td style="text-align:right;">{{ $totalearn }}</td>
                                </tr>
                                <tr>
                                    <td>GROSS PAY</td>
                                    <td style="text-align:right;  border-top: 1px solid black;">{{ $grosspay }}</td>
                                </tr>
                                <tr>
                                    <td>TOTAL DEDUCTIONS</td>
                                    <td style="text-align:right; border-bottom: 1px solid black;">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="bodytd" width="30%"colspan="2" style="vertical-align: top; border-right: none; border-bottom:none; ">
                            <table class="innertables" style="border: none;  width: 100%;">
                                <tr>
                                    <td colspan="4" style="text-align: center; border-bottom: 1px solid black; border-left: none;">
                                        <b>RECEIVABLES</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>OVERTIME(1.5)</td>
                                    <td style="text-align: center;">{{ number_format((float)$row['OTAMT1'], 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ (float)$row['OTHRS1'] != 0 ? number_format((float)$row['OTHRS1'] / (float)$row['OTAMT1'], 2, '.', ',') : '00.00' }}</td>
                                    <td  style="text-align: right;">{{ number_format((float)$ot1val, 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>OVERTIME(2.0)</td>
                                    <td style="text-align: center;">{{ number_format((float)$row['OTAMT2'], 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ (float)$row['OTHRS2'] != 0 ? number_format((float)$row['OTHRS2'] / (float)$row['OTAMT2'], 2, '.', ',') : '00.00' }}</td>
                                    <td  style="text-align: right;">{{ number_format((float)$ot2val, 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>OT3-mercantile</td>
                                    <td>after 4.00</td>
                                    <td>{{$mercantile_triple_othrs}} hours</td>
                                    <td>{{$ot3val}}</td>
                                </tr>
                                <tr>
                                    <td>sunday 1.5xday rate</td>
                                    <td>8.00-4.00</td>
                                    <td>{{$sunday_workhrs}} hours</td>
                                    <td>{{$ot4tl_sundayval}}</td>
                                </tr>
                                <tr>
                                    <td>sunday 2.0xhour rate</td>
                                    <td>after 4.00</td>
                                    <td>{{$sunday_double_othrs}} hours</td>
                                    <td>{{$ot7tl_sunday_double_otval}}</td>
                                </tr>
                                <tr>
                                    <td>poya 1.5xday grosspay</td>
                                    <td>8.00-4.00</td>
                                    <td>{{$poya_workhrs}} hours</td>
                                    <td>{{$ot4tl_poyaval_original}}</td>
                                </tr>
                                <tr>
                                    <td>poya 1.5xhour rate</td>
                                    <td>after 4.00</td>
                                    <td>{{$poya_normal_othrs}} hours</td>
                                    <td>{{$ot6tl_poya_normal_otval}}</td>
                                </tr>
                                <tr>
                                    <td>mercantile 2.0xday grosspay</td>
                                    <td>8.00-4.00</td>
                                    <td>{{$mercantile_workhrs}} hours</td>
                                    <td>{{$ot5tl_mercantileval_original}}</td>
                                </tr>
                                <tr>
                                    <td><strong>poya 0.5x + mercantile 1.0xday</strong></td>
                                    <td>8.00-4.00</td>
                                    <td></td>
                                    <td>{{$ot4tl_poya_ot5tl_mercantile_payval}}</td>
                                </tr>
                                @if((float)$row['INCNTV_EMP'] != 0)
                                @endif
                                <tr>
                                    <td colspan="2">Attendance Allow.</td>
                                    <td colspan="2" style="text-align: right;">{{ number_format((float)$row['INCNTV_EMP'], 2, '.', ',') }}</td>
                                </tr>
                                

                                @if((float)$row['ATTBONUS_W'] != 0)
                                @endif
                                <tr>
                                    <td colspan="2">INCENTIVE.</td>
                                    <td colspan="2" style="text-align: right;">{{ number_format((float)$row['ATTBONUS_W'], 2, '.', ',') }}</td>
                                </tr>
                                

                                @if((float)$row['ded_tp'] != 0)
                                @endif
                                <tr>
                                    <td colspan="2">Food Allow.</td>
                                    <td colspan="2" style="text-align: right;">{{ number_format((float)$row['ded_tp'], 2, '.', ',') }}</td>
                                </tr>
                                
                                <tr>
                                    <td colspan="2">Grade Allow.</td>
                                    <td colspan="2" style="text-align: right;">{{ number_format((float)$row['ded_fund_1'], 2, '.', ',') }}</td>
                                </tr>
                                
                                <tr>
                                    <td colspan="2">Other Additions</td>
                                    <td colspan="2" style="text-align: right;">{{ number_format((float)$row['add_other'], 2, '.', ',') }}</td>
                                </tr>
                                
                            </table>
                        </td>
                        <td class="bodytd" width="30%"colspan="2" style="vertical-align: top; border-bottom:none; border-right: none; ">
                            <table class="innertables"  style="border: none;  width: 100%;">
                                <tr>
                                    <td colspan="2" style="text-align: center; border-bottom: 1px solid black; border-left: none;"><b>DEDUCTION</b></td>
                                </tr>
                                <tr>
                                    <td> EPF 8%</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</td>
                                </tr>

                                @if((float)$row['INCNTV_DIR'] != 0)
                                @endif
                                <!--tr>
                                    <td>Bank Charges</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['INCNTV_DIR'], 2, '.', ',') }}</td>
                                </tr-->
                                

                                @if((float)$row['LOAN'] != 0)
                                @endif
                                <tr>
                                    <td>LOAN</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['LOAN'], 2, '.', ',') }}</td>
                                </tr>
                                
                                
                                @if((float)$row['PAYE'] != 0)
                                <tr>
                                    <td>PAYE</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['PAYE'], 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$row['sal_adv'] != 0)
                                @endif
                                <tr>
                                    <td>ADVANCE</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['sal_adv'], 2, '.', ',') }}</td>
                                </tr>
                                
                                @if((float)$row['ded_IOU'] != 0)
                                <tr>
                                    <td>Late Deduct.</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ded_IOU'], 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                
                                <tr>
                                    <td>Other Deductions</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ded_other'], 2, '.', ',') }}</td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>

                        <tr>
                            <td class="bodytd" style=" border-left:none; border-right:none;border-bottom:none;border-top:none;">NET SALARY</td>
                            <td class="bodytd" style="border-left:none; border-bottom:none;border-top:none;  border-right:none; text-align: right;"> &nbsp;{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</td>
                            <td class="bodytd" style="border-right:none;border-bottom:none; border-right:none; border-top:none;">TOTAL</td>
                            <td class="bodytd" style="border-left:none; border-bottom:none; border-right:none;  text-align: right;">&nbsp;{{ $totalearn }}</td>
                            <td class="bodytd" style="text-align: left; border-right:none; border-bottom:none; border-top:none;">TOTAL</td>
                            <td class="bodytd" style="text-align: right; border-left:none; border-bottom:none; border-right:none;">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td class="bodytd" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; text-align:center;">YOUR NET SALARY AS ABOVE IS SEND TO</td>
                            <td class="bodytd" colspan="2" style="text-align:center;  border-right:none;"><b>ATTENDANCE SUMMARY</b></td>
                            <td class="bodytd" colspan="2" style="text-align:center;  border-right:none;"><b>EMPLOYER</b></td>
                        </tr>

                        <tr>
                        <td class="bodytd" colspan="2" style="vertical-align: top; border-left:none; border-right:none;border-bottom:none; border-top:none; text-align:center;">
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td style="text-align:center;">{{ $row['bank_name'] }} - {{ $row['bank_branch'] }}  </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">ACCOUNT NO - {{ $row['bank_accno'] }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                        <td  colspan="2" class="bodytd" style=" vertical-align: top; border-right:none;border-bottom:none; border-right:none; border-top:none;  ">
                            <table class="innertables" style="border: none;">
                                <!--tr>
                                    <td colspan="2">Monthly Working Days</td>
                                    <td style="text-align: right;">payment_period_total_days</td>
                                    </tr-->
                                <tr>
                                    <td colspan="2">Attendance</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['work_week_days'], 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                    <td>NO PAY DAYS</td>
                                    <td style="text-align: center;">{{ (float)$row['NOPAYCNT'] != 0 ? number_format((float)$row['NOPAY'] / (float)$tot_nopay_days, 2, '.', ',') : '00.00' }}</td>
                                    <td style="text-align: right; ">{{ number_format((float)$row['NOPAYCNT'], 2, '.', ',') }}</td>
                                    </tr>
                                    <!--tr>
                                    <td colspan="2">LATE ATTENDANCE H/M</td>
                                    <td style="text-align: right;">00.00</td>
                                    </tr-->
                                
                            </table>
                        </td>
                        <td colspan="2" class="bodytd"
                            style=" vertical-align: top; text-align: left; border-right:none; border-bottom:none; border-top:none;">
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td>EPF 12% </td>
                                    <td style="text-align: right;">
                                        {{ number_format((float)$row['EPF12'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>ETF 3% </td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ETF3'], 2, '.', ',') }}
                                    </td>
                                </tr>

                            </table>
                        </td>
                        </tr>

                        <tr>
                        <td class="bodytd" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; "></td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none;  border-right:none;"><b></b></td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none; border-top:none; border-right:none;"></td>
                    </tr>
                        <tr>
                        <td class="bodytd" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; border-top:none; padding-top:15px; font-size:11px; text-align:left; vertical-align:bottom;">Printed On :  {{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y H:i:s') }}</td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none;  border-top:none; border-right:none; padding-top:15px;"><b></b></td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none; border-top:none; border-right:none; padding-top:15px;">
                            .......................................... <br>EMPLOYEE'S SIGNATURE</td>
                    </tr>
                </tbody>
            </table>
            @endif
            
            @php $check++ @endphp
        	
        @endfor
      
  </body>
</html>