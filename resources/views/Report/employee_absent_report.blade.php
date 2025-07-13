<?php $page_stitle = 'Report on Employees Resignation - Multi Offset HRM'; ?>
@extends('layouts.app')

@section('content')
<main> 
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.reports_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card mb-2">
            <div class="card-body">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        {{-- <div class="col">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm">
                            </select>
                        </div> --}}
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Date From</label>
                           <input type="date" name="selectdatefrom" id="selectdatefrom" class="form-control form-control-sm">
                        </div>
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Date To</label>
                           <input type="date" name="selectdateto" id="selectdateto" class="form-control form-control-sm">
                        </div>
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                                <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                                @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                }  
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <br>
                            <button type="button" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                             <div class="col-md-4">
                                    {{-- <button type="button" class="btn btn-sm btn-outline-primary excel-btn"> Download Excel 
                                         </button>  --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger pdf-btn"
                                        onclick="generatePDF();"> Download PDF
                                    </button>
                                </div><br>
                            <table class="table table-striped table-bordered table-sm small" id="emptable">
                                <thead>
                                <tr>
                                    <th>EMP ID</th>
                                    <th>Name with Initial</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Address</th>
                                    <th>Phone No</th>
                                    <th>Number Of Days Absent</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
                
              
@endsection
@section('script')
<script>
$(document).ready(function() {

    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#employeereportmaster').addClass('navbtnactive');

    $('#department').select2({
    width: '100%'
    });


    $('#btn-filter').click(function() {
        let selectdatefrom = $('#selectdatefrom').val();
        let selectdateto = $('#selectdateto').val();
        let department = $('#department').val();
        let departmentname = $('#department option:selected').text();

            if(!selectdatefrom){
                $('#selectdatefrom').focus();
                return false;
            }
            if(!selectdateto){
                $('#selectdateto').focus();
                return false;
            }
            if(!department){
                $('#department').focus();
                return false;
            }
       

        $('#emptable').DataTable({
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                            {
                                extend: 'excelHtml5',
                                title: 'Report on Employees Absent ('+selectdatefrom+'-'+selectdateto+') '+departmentname+' -ShapeUp  HRM   '
                            }
                        ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{url('/get_absent_employees')}}",
                "data": {'selectdatefrom':selectdatefrom,
                         'selectdateto':selectdateto,
                         'department':department
                },
            },
            columns: [
                { data: 'emp_id' },
                { data: 'emp_name_with_initial' },
                { data: 'departmentname' },
                { data: 'location' },
                { data: 'emp_address' },
                { data: 'emp_mobile' },
                { data: 'absent_days' },
            ],
            "bDestroy": true,
            "order": [[ 0, "ase" ]],
        });
    });
} );

async function generatePDF() {

        
        const { jsPDF } = window.jspdf;
         const { autoTable } = window.jspdf;
        
        const doc = new jsPDF({
            format: 'a4',
            unit: 'mm'
        });

        const currentDate = new Date().toISOString().split('T')[0];

        
        // Get data from DataTable
        const table = $('#emptable').DataTable();
        const data = table.rows({ search: 'applied' }).data().toArray();
        
        // Prepare columns for PDF
        const columns = [
            { header: 'ID', dataKey: 'emp_id' },
            { header: 'Name', dataKey: 'emp_name_with_initial' },
            { header: 'Department', dataKey: 'departmentname' },
            { header: 'Designation', dataKey: 'location' },
            { header: 'Address', dataKey: 'emp_address' },
            { header: 'Phone', dataKey: 'emp_mobile' },
            { header: 'Absent Days', dataKey: 'absent_days' }
        ];
        
        // Prepare rows for PDF
        const rows = data.map(item => ({
            emp_id: item.emp_id,
            emp_name_with_initial: item.emp_name_with_initial,
            departmentname: item.departmentname,
            location: item.location,
            emp_address: item.emp_address,
            emp_mobile: item.emp_mobile,
            absent_days: item.absent_days
        }));

        // Add title and date
        doc.setFontSize(18);
        doc.setFont('helvetica', 'bold');
        doc.text('Employee Absent report Summery ', 105, 20, { align: 'center' });
        
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Generated on: ' + currentDate, 105, 28, { align: 'center' });

        // Generate table
        doc.autoTable({
            head: [columns.map(col => col.header)],
            body: rows.map(row => columns.map(col => row[col.dataKey])),
            startY: 35,
            margin: { top: 30 },
            styles: {
                fontSize: 9,
                cellPadding: 3,
                lineWidth: 0.2,
                lineColor: [0, 0, 0],
                halign: 'center',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [220, 220, 220],
                textColor: [0, 0, 0],
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: { cellWidth: 20, halign: 'center' },
                1: { cellWidth: 30, halign: 'left' },
                2: { cellWidth: 30, halign: 'left' },
                3: { cellWidth: 30, halign: 'left' },
                4: { cellWidth: 30, halign: 'left' },
                5: { cellWidth: 30, halign: 'center' },
                6: { cellWidth: 20, halign: 'center' }
            }
        });

        doc.save('Employee_Absent_Report_Summery' + currentDate + '.pdf');
}

function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

</script>

@endsection