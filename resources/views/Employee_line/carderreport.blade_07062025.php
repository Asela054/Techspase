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
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Date</label>
                                <input type="date" class="form-control form-control-sm" name="reportdate" id="reportdate" required >
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
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dt1">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Line</th>
                                    <th class="d-none">Line id</th>
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
        $('#employeedetailsreport').addClass('navbtnactive');

        let company = $('#company');
        let department = $('#department');

        $('#banks').select2({width: '100%'});
     
        company.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("company_list_sel2")}}',
                dataType: 'json',
                data: function(params) {
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
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        company: company.val()
                    }
                },
                cache: true
            }
        });

        function load_dt(department,reportdate){
            $('#dt1').DataTable({
                "columnDefs": [ {
                    "targets": -1,
                    "orderable": false
                },
                {
                "targets": 3,
                "visible": false,
                "searchable": false
            } ],
                "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
                dom: 'Blfrtip',
              "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Carder Report',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    {
                        extend: 'print',
                        title: 'Carder Report',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function (win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                processing: true,
                serverSide: false,
                ajax: {
                    "url": "{{url('/generatecarderreport')}}",
                    "type": "POST",
                    "data": {
                         _token: '{{ csrf_token() }}',
                         department :department,
                         reportdate :reportdate
                        },
                },
                columns: [
                    { data: 'emp_id', title: 'Employee ID' },
                    { data: 'name', title: 'Name' },
                    { data: 'line_name', title: 'Line Name' },
                    { data: 'line_id', title: 'Line ID' }
                ],
                "bDestroy": true,
                "order": [[ 3, "asc" ]],
            });
        }

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department').val();
            let reportdate = $('#reportdate').val();
            load_dt(department,reportdate);
        });
    });
</script>
@endsection