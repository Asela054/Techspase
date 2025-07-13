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
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Route</label>
                                <select name="route" id="route" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    @foreach($transportroute as $route)
                                        <option value="{{ $route->id }}">{{ $route->name }}</option>
                                    @endforeach
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
                                <table class="table table-striped table-bordered table-sm small" id="ottransportreporttable" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>EMP ID</th>
                                            <th>Name with Initial</th>
                                            <th>Department</th>
                                            <th>Date</th>
                                            <th>Route</th>
                                            <th>Vehicle</th>
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
    $('#otallocationreport').addClass('navbtnactive');

    let company = $('#company');
    let employee = $('#employee');
    let route = $('#route');
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

    employee.select2({
        placeholder: 'Select a Employee',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_for_ot")}}',
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

    route.select2({
        placeholder: 'Select Route',
        width: '100%',
        allowClear: true
    });

    load_dt();
    
    function load_dt() {
        $('#ottransportreporttable').DataTable({
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-default btn-sm',
                    title: 'Transport Report',
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Print',
                    className: 'btn btn-default btn-sm',
                    title: 'Transport Report',
                }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{url('/transport_report_list')}}",
                "data": function(d) {
                    d.route = $('#route').val();
                    d.employee = $('#employee').val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                { data: 'emp_id', name: 'employees.emp_id' },
                { data: 'emp_name_with_initial', name: 'employees.emp_name_with_initial' },
                { data: 'dept_name', name: 'departments.name' },
                { data: 'date', name: 'ot_allocation.date' },
                { data: 'route', name: 'transport_routes.name' },
                { data: 'vehicle', name: 'transport_vehicles.vehicle_number' }
            ],
            "order": [[4, 'asc']], 
            "bDestroy": true,
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({page: 'current'}).nodes();
                var last = null;
                
                api.column(4, {page: 'current'}).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group bg-gray-200"><td colspan="6"><strong>Route: ' + group + '</strong></td></tr>'
                        );
                        last = group;
                    }
                });
            }
        });
    }

    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        $('#ottransportreporttable').DataTable().ajax.reload();
    });
});
</script>
@endsection