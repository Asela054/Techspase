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
                                <select name="company" id="company" class="form-control form-control-sm" required>
                                    <option value="">Select Company</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Shift Type*</label>
                                <select name="shift" id="shift" class="form-control form-control-sm"
                                    required readonly style="pointer-events: none">
                                    <option value="">Select Shift</option>
                                    <option selected value="2">Night</option>
                                    <!-- <option value="1">Day</option> -->
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
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
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="nightshiftreporttable" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>EMP ID</th>
                                            <th>Name with Initial</th>
                                            <th>Department</th>
                                            <th>Shift Type</th>
                                            <th>Date</th>
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

    let company = $('#company');
    let department = $('#department');
    let employee = $('#employee');
    let shift = $('#shift');
    let from_date = $('#from_date');
    let to_date = $('#to_date');

    // Set default dates
    let today = new Date();
    let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    from_date.val(firstDay.toISOString().split('T')[0]);
    to_date.val(today.toISOString().split('T')[0]);

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

    employee.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company.val(), 
                    department: department.val() 
                }
            },
            cache: true
        }
    });

    company.on('change', function() {
        department.val(null).trigger('change');
        employee.val(null).trigger('change');
    });

    department.on('change', function() {
        employee.val(null).trigger('change');
    });

    load_dt();
    
    function load_dt() {
        $('#nightshiftreporttable').DataTable({
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-default btn-sm',
                    title: 'Night Shift Report', 
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Print',
                    className: 'btn btn-default btn-sm',
                    title: 'Night Shift Report', 
                }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{url('/night_shift_report_list')}}",
                "data": function(d) {
                    d.department = $('#department').val();
                    d.shift = $('#shift').val();
                    d.employee = $('#employee').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                { data: 'emp_id', name: 'employees.emp_id' },
                { data: 'emp_name_with_initial', name: 'employees.emp_name_with_initial' },
                { data: 'dept_name', name: 'departments.name' },
                {
                    data: 'shift_id',
                    name: 'employeeshifts.shift_id',
                    render: function(data, type, row) {
                        if (data == 2) {
                            return 'Night Shift';
                        } else if (data == 1) {
                            return 'Day Shift';
                        } else {
                            return 'Unknown shift'; 
                        }
                    }
                },
                { 
                    data: 'date_from', 
                    name: 'employeeshifts.date_from',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
            ],
            "order": [[2, 'asc']], 
            "bDestroy": true,
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({page: 'current'}).nodes();
                var last = null;
                
                api.column(2, {page: 'current'}).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group bg-gray-200"><td colspan="5"><strong>Department: ' + group + '</strong></td></tr>'
                        );
                        last = group;
                    }
                });
            }
        });
    }

    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        $('#nightshiftreporttable').DataTable().ajax.reload();
    });
});
</script>
@endsection