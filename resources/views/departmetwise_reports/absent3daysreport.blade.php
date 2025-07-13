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
                                           placeholder="yyyy-mm-dd" required>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd" required>
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
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Absent Started Date</th>
                                    <th>Continuous Absent Dates Count</th>
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

        function load_dt(department, from_date, to_date) {
            $('#dt1').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 100, 500, 1000],
                    [10, 25, 50, 100, 500, 1000]
                ],
                processing: true,
                serverSide: false,
                ajax: {
                    "url": "{{url('/generateabsentdaysreport')}}",
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
                        data: 'start_date',
                    },
                    {
                        data: 'days_count',
                    }
                ],
                "bDestroy": true,
                "order": [[0, "asc"]],
            });
        }

        $('#formFilter').on('submit', function (e) {
            e.preventDefault();
            let department = $('#department').val();
             let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            load_dt(department, from_date,to_date);
        });
    });

        async function generatePDF() {
            const { jsPDF } = window.jspdf;
            const { autoTable } = window.jspdf;

            const doc = new jsPDF({
                format: 'a4',
                unit: 'mm'
            });

            const currentDate = new Date().toISOString().split('T')[0];

            let firstPage = true;

            doc.autoTable({
                html: '#dt1',
                theme: 'grid',
                startY: 40, 
                margin: {
                    top: 35,
                    left: 20,
                    right: 20
                },
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
                    0: { cellWidth: 25, halign: 'center' },
                    1: { cellWidth: 50, halign: 'center' },
                    2: { cellWidth: 35, halign: 'center' },
                    3: { cellWidth: 35, halign: 'center' }
                },
                didDrawPage: function (data) {
                    const pageWidth = doc.internal.pageSize.getWidth();

                    if (firstPage) {
                        // Page 1 Header
                        doc.setFontSize(18);
                        doc.setFont('Helvetica', 'bold');
                        doc.setTextColor(40);
                        centerText('Absent More Than 3days Report', 20, doc);

                        doc.setFontSize(14);
                        doc.setFont('Helvetica', 'bold');
                        centerText('Techspace HRM', 28, doc);

                        doc.setFontSize(10);
                        doc.setFont('Helvetica', 'normal');
                        centerText('Date: ' + currentDate, 35, doc);

                        doc.setDrawColor(200, 200, 200);
                        doc.line(20, 38, pageWidth - 20, 38);
                    } else {
                        // Add space at top for subsequent pages
                        doc.setFontSize(10);
                        doc.setFont('Helvetica', 'normal');
                        doc.text('Date: ' + currentDate, 20, 15); // Top-left date
                    }

                    firstPage = false;
                }
            });

            doc.save('Absent More Than 3days Report_' + currentDate + '.pdf');
        }

        function centerText(text, y, doc) {
            const pageWidth = doc.internal.pageSize.getWidth();
            const textWidth = doc.getStringUnitWidth(text) * doc.internal.getFontSize() / doc.internal.scaleFactor;
            const x = (pageWidth - textWidth) / 2;
            doc.text(text, x, y);
        }


</script>
@endsection