<!DOCTYPE html>
<html lang="si">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { 
            size: 105mm 297mm;
            margin: 5mm 5mm 5mm 5mm;
        }
        body {
            font-family: iskpota;
            font-size: 13px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 4px;
            vertical-align: top;
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
            
            $h12 = $ot4tl_poya_ot5tl_mercantile_payval;
            $i12 = $row['poya_work_days']; $i13 = $row['holiday_poya_nopay_days'];
            $j12 = $row['mercantile_work_days']; $j13 = $row['holiday_mercantile_nopay_days'];
            $i_j = (($i13*0.5)+($j13*1)+(($i12-$i13)*1.5)+(($j12-$j13)*2));
            $h13 = ($i_j>0)?($h12/$i_j):0;
            
            
            $ot4tl_poya_payval = ($h13*$i13*0.5)+($h13*($i12-$i13)*1.5);
            $ot5tl_mercantile_payval = $ot4tl_poya_ot5tl_mercantile_payval-$ot4tl_poya_payval;
            
            @endphp
            <table id="maintable">
                <tbody>
                    <tr>
                        <td style="text-align: center;"><strong style="font-size: 14px;font-family: Arial, sans-serif;">{{ $company_name }} - Employee</strong></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><strong style="font-size: 14px;font-family: Arial, sans-serif;color: red;">PAY SLIP</strong></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;font-size: 14px;font-family: Arial, sans-serif;">Pay slip for the month of {{$paymonth_name}}</td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border-collapse: separate;border-spacing: 0;border: 2px solid #000;">
                                <tr>
                                    <td>සේවා අංකය</td>
                                    <td>{{ $row['emp_empno'] }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>සේ. අ. අ අංකය</td>
                                    <td>{{ $row['emp_epfno'] }}</td>
                                    <td>පැමිණි දින</td>
                                    <td>{{ $row['WORKDAYSCNT'] }}</td>
                                </tr>
                                <tr>
                                    <td>නම</td>
                                    <td colspan="3">{{ $row['emp_first_name'] }}</td>
                                </tr>
                                <tr>
                                    <td>තනතුර</td>
                                    <td colspan="3">{{ $row['emp_designation'] }}</td>
                                </tr>
                                <tr>
                                    <td>අංශය</td>
                                    <td colspan="3">{{ $row['department'] }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td></td>
                                    <td style="text-align: right;">(Rs)</td>
                                </tr>
                                <tr>
                                    <td>මුලික වැටුප</td>
                                    <td style="text-align: right;">{{ number_format((float)($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']), 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">(අයවැය දීමනාව 2005 හා 2015 ඇතුලත්ව)</td>
                                </tr>
                                <tr>
                                    <td>වැටුප් කප්පාදුව</td>
                                    <td style="text-align: right;">{{ number_format((float)'0', 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>වැටුප් රහිත අඩු කිරීම්</td>
                                    <td style="text-align: right;">{{ number_format((float)$nopaystr, 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">සේ. අ. සා. අ සදහා මුලික වැටුප</td>
                                    <td style="text-align: right;">{{ $netbasic }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong><u>වෙනත් ගෙවීම්</u></strong></td>
                                </tr>
                                @if((float)$ot1val != 0)
                                <tr>
                                    <td>සාමාන්‍ය 1.5 අතිකාල ගෙවීම් ({{ number_format((float)$row['OTAMT1'], 2, '.', ',') }})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot1val, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$ot7tl_sunday_double_otval != 0)
                                <tr>
                                    <td>ඉරිදා 2 අතිකාල ගෙවීම් ({{$sunday_double_othrs}})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot7tl_sunday_double_otval, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$ot6tl_poya_normal_otval != 0)
                                <tr>
                                    <td>පෝය 1.5 අතිකාල ගෙවීම් ({{$poya_normal_othrs}})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot6tl_poya_normal_otval, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$ot3val != 0)
                                <tr>
                                    <td>වෙළද නිවාඩු 3.0 අතිකාල ගෙවීම ({{$mercantile_triple_othrs}})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot3val, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$ot4tl_poya_payval != 0)
                                <tr>
                                    <td>පෝය ගෙවීම් ({{$poya_workhrs}})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot4tl_poya_payval, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$ot5tl_mercantile_payval != 0)
                                <tr>
                                    <td>වෙළද නිවාඩු ගෙවීම් ({{$mercantile_workhrs}})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot5tl_mercantile_payval, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                @if((float)$ot4tl_sundayval != 0)
                                <tr>
                                    <td>ඉරිදා ගෙවීම් ({{$sunday_workhrs}})</td>
                                    <td style="text-align: right;">{{ number_format((float)$ot4tl_sundayval, 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                
                                <tr>
                                    <td>පැමිණීමේ දීමනාව </td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ATTBONUS_W'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>නිෂ්පාදන දිරි දීමනාව</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['INCNTV_EMP'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>ගමන් ගාස්තු</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['add_transport'], 2, '.', ',') }}</td>
                                </tr>
                                <!--tr>
                                    <td>විශේෂ දීමනාව</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['add_other'], 2, '.', ',') }}</td>
                                </tr-->
                                
                                @if((float)$row['add_other'] != 0)
                                <tr>
                                    <td>වෙනත් එකතුවීම්</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['add_other'], 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                
                                <tr>
                                    <td><b>මුළු එකතුවීම්</b></td>
                                    <td style="text-align: right;border-top: 1px solid #000;">{{ $totalearn }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><b>මුළු වැටුප</b></td>
                                    <td style="text-align: right;"><b>{{ $grosspay }}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b><u>අඩු කිරීම</u></b></td>
                                </tr>
                                <tr>
                                    <td>සේ.අ.අ 8%</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>උපයන විට ගෙවීමේ බදු</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['PAYE'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>ණය අඩු කිරීම්</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['LOAN'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>ප්‍රමාදවීම අඩු කිරීම ()</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ded_IOU'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>සුබසාදන</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ded_fund_1'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>ආහාර සදහා අඩු කිරීම</td>
                                    <td style="text-align: right;">{{ number_format((float)'0', 2, '.', ',') }}</td>
                                </tr>
                                
                                @if((float)$row['ded_other'] != 0)
                                <tr>
                                    <td>වෙනත් අඩු කිරීම</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ded_other'], 2, '.', ',') }}</td>
                                </tr>
                                @endif
                                
                                <tr>
                                    <td><b>මුළු අඩු කිරීම</b></td>
                                    <td style="text-align: right;border-top: 1px solid #000;">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><b>ශුද්ධ වැටුප</b></td>
                                    <td style="text-align: right;border-top: 1px solid #000;border-bottom: 2px double #000;"><b>{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</b></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="border-collapse: separate;border-spacing: 0;border: 2px solid #000;">
                                <tr>
                                    <td colspan="2">සේව්‍යයාගේ ගෙවීම්</td>
                                </tr>
                                <tr>
                                    <td>සේ.අ.අ 12.00%</td>
                                    <td>{{ number_format((float)$row['EPF12'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>සේ.බ.අ 3.00%</td>
                                    <td>{{ number_format((float)$row['ETF3'], 2, '.', ',') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagebreak>
            @endif
            
            @php $check++ @endphp
        	
        @endfor
  </body>
</html>