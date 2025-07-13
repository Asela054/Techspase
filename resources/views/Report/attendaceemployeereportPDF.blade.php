<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        .report-table, .report-table th, .report-table td {
            border: 1px solid black;
        }
        .report-table th, .report-table td {
            padding: 2px;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach ($pdfData as $data)
    <table class="report-table">
        <tr class="titlerow">
            <td style="border-bottom: none; border-right:none; border-left:none; text-align:left;" colspan="6"><strong>Emp No:</strong> {{ $data['employee']->emp_id }} </td>
            <td style="border-bottom: none; border-left:none; border-right:none; text-align:left;"  colspan="6"><strong>Department:</strong> {{ $data['employee']->departmentname }} </td>
        </tr>
        <tr class="titlerow">
            <td style="border-bottom: none; border-top: none; border-right:none; border-left:none; text-align:left;" colspan="6"><strong>Name:</strong> {{ $data['employee']->emp_fullname }} </td>
            <td style="border-bottom: none; border-top: none; border-right:none; border-left:none; text-align:left;" colspan="6"><strong>Gender:</strong> {{ $data['employee']->emp_gender }} </td>
        </tr>
        <tr class="titlerow"> 
            <td style="border-bottom: none; border-top: none; border-right:none; border-left:none; text-align:left;" colspan="6"><strong>Designation:</strong> {{ $data['employee']->jobtitlename }} </td>
            <td style="border-bottom: none; border-top: none; border-right:none; border-left:none; text-align:left;"  colspan="6"><strong>Shift:</strong> {{ $data['employee']->shiftname }} </td>
        </tr>
        <thead>
            <tr>
                <th class="nowrap">In Date</th>
                <th class="nowrap">Out Date</th>
                <th>Day Type</th>
                <th>Shift</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Late Min</th>
                <th>OT Hr:Mi</th>
                <th>DOT Hr:Mi</th>
                <th>TOT Hr:Mi</th>
                <th>Leave Type</th>
                <th>Leave Day</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['attendance'] as $attendance)
            <tr>
                <td>{{ $attendance['in_date'] }}</td>
                <td>{{ $attendance['out_date'] }}</td>
                <td>{{ $attendance['day_type'] }}</td>
                <td>{{ $attendance['shift'] }}</td>
                <td>{{ $attendance['in_time'] }}</td>
                <td>{{ $attendance['out_time'] }}</td>
                <td>{{ $attendance['late_min'] }}</td>
                <td>{{ $attendance['ot_hours'] }}</td>
                <td>{{ $attendance['double_ot'] }}</td>
                <td>{{ $attendance['triple_ot'] }}</td>
                <td>{{ $attendance['leave_type'] }}</td>
                <td>{{ $attendance['leave_days'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="page-break"></div>
@endforeach

</body>
</html>
