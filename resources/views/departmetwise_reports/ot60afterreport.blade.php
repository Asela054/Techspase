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
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm" required>
                                </select>
                            </div>
                             <div class="col-md-3 " id="div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                                <div class="col-md-4">
                                    {{-- <button type="button" class="btn btn-sm btn-outline-primary excel-btn"> Download Excel 
                                         </button>  --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger pdf-btn"
                                        onclick="generatePDF();"> Download PDF
                                    </button>
                                </div><br>
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dt1">
                                <thead>
                                <tr>
                                    <th>EMP ID</th>
                                    <th>Name</th>
                                    <th>Total OT Hours</th>
                                    <th>Over 60hrs</th>
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
    $(document).ready(function () {

        $('#report_menu_link').addClass('active');
        $('#report_menu_link_icon').addClass('active');
        $('#employeereportmaster').addClass('navbtnactive');

        let company = $('#company');
        let department = $('#department');

        $('#banks').select2({
            width: '100%'
        });

        company.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("company_list_sel2")}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
        });

        department.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("department_list_sel2")}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        company: company.val()
                    }
                },
                cache: true
            }
        });

        function load_dt(department, from_date,to_date) {
            $('#dt1').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 100, 500, 1000],
                    [10, 25, 50, 100, 500, 1000]
                ],
                processing: true,
                serverSide: false,
                ajax: {
                    "url": "{{url('/generatehrotreport')}}",
                    "type": "POST",
                    "data": {
                        _token: '{{ csrf_token() }}',
                        department: department,
                        from_date: from_date,
                        to_date: to_date
                    },
                },
                columns: [{
                        data: 'emp_id',
                    },
                    {
                        data: 'emp_name',
                    },
                    {
                        data: 'total_ot_hours',
                    },
                    {
                        data: 'ot_hours_over_60',
                    }
                ],
                "bDestroy": true,
                "order": [
                    [0, "asc"]
                ],
            });
        }

        $('#formFilter').on('submit', function (e) {
            e.preventDefault();
            let department = $('#department').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            load_dt(department,from_date,to_date);
        });
    });

   
      async function generatePDF() {

        const { jsPDF } = window.jspdf;
        const { autoTable } = window.jspdf;

        const doc = new jsPDF({
            format: 'a4',
            unit: 'mm'
        });

        const currentDate = new Date().toLocaleDateString();
        const month = $('#from_date').val() ? new Date($('#from_date').val()).toLocaleString('default', { month: 'long', year: 'numeric' }) : '';

        // Get data from DataTable
        const table = $('#dt1').DataTable();
        const data = table.rows({ search: 'applied' }).data().toArray();

        // Prepare columns for PDF
        const columns = [
            { header: 'Employee ID', dataKey: 'emp_id' },
            { header: 'Employee Name', dataKey: 'emp_name' },
            { header: 'Total OT Hours', dataKey: 'total_ot_hours' },
            { header: 'OT Hours Over 60', dataKey: 'ot_hours_over_60' }
        ];

        // Prepare rows for PDF
        const rows = data.map(item => ({
            emp_id: item.emp_id,
            emp_name: item.emp_name,
            total_ot_hours: item.total_ot_hours,
            ot_hours_over_60: item.ot_hours_over_60
        }));

        // Add report header
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(40);
        doc.text('Over Time Summary Report', 105, 20, { align: 'center' });

        // Add month and date range
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text(`Month: ${month}`, 20, 30);
        doc.text(`From: ${$('#from_date').val()}`, 105, 30, { align: 'center' });
        doc.text(`To: ${$('#to_date').val()}`, 180, 30, { align: 'right' });

        // Add company name
       
        // Add divider line
        doc.setDrawColor(200, 200, 200);
        doc.line(20, 50, 190, 50);

        // Generate table
        doc.autoTable({
            head: [columns.map(col => col.header)],
            body: rows.map(row => columns.map(col => row[col.dataKey])),
            startY: 55,
            margin: { top: 10, left: 20, right: 20 },
            tableWidth: 'wrap',
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
                0: { halign: 'center' },
                1: { halign: 'left' },
                2: { halign: 'center' },
                3: { halign: 'center' }
            }
        });
        doc.save(`OT_Summary_Report_${currentDate.replace(/\//g, '-')}.pdf`);
}

</script>
@endsection