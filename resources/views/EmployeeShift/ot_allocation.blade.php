@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.shift_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        @can('employee-ot-allocation-create')
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right mr-2" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add Employee</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm fa-pull-right mr-2" name="csv_upload"
                            id="csv_upload"><i class="fas fa-plus mr-2"></i>Add - CSV Upload</button>
                            @endif
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>Date</th>
                                        <th class="text-right">Action</th>
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
    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add OT Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Date*</label>
                                        <input type="date" name="date" id="date" class="form-control form-control-sm" max="" min="" required />
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Time From*</label>
                                        <input type="time" name="time_from" id="time_from" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Time To*</label>
                                        <input type="time" name="time_to" id="time_to" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select name="employee" id="employee" class="form-control form-control-sm" required></select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="transport" name="transport" value="1">
                                            <label class="custom-control-label" for="transport">Transport Applicable</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="meal" name="meal" value="1">
                                            <label class="custom-control-label" for="meal">Meal Applicable</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Route</label>
                                        <select id="route" name="route" class="form-control form-control-sm">
                                            <option value="">Select Route</option>
                                            @foreach ($transportroute as $transportroutes)
                                                <option value="{{$transportroutes->id}}" >{{$transportroutes->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Vehicle</label>
                                        <select id="vehicle" name="vehicle" class="form-control form-control-sm">
                                            <option value="">Select Vehicle</option>
                                            @foreach ($transportvehicle as $transportvehicles)
                                                <option value="{{$transportvehicles->id}}" >{{$transportvehicles->vehicle_number}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="col-6">
                                    <button type="button" id="formsubmit"
                                        class="btn btn-primary btn-sm px-4 fa-pull-right"><i
                                            class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                    <button type="button" name="Btnupdatelist" id="Btnupdatelist"
                                        class="btn btn-primary btn-sm px-4 fa-pull-right" style="display:none;"><i
                                            class="fas fa-plus"></i>&nbsp;Update List</button>
                                    </div>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="detailsid" id="detailsid">

                            </form>
                        </div>
                        <div class="col-12 mt-3">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Employee Name</th>
                                        <th>Date</th>
                                        <th class="d-none">Time From</th>
                                        <th class="d-none">Time To</th>
                                        <th class="d-none">Transport</th>
                                        <th class="d-none">Meal</th>
                                        <th class="d-none">Route</th>
                                        <th class="d-none">Vehicle</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableorderlist"></tbody>
                            </table>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder"
                                    class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                        class="fas fa-plus"></i>&nbsp;Create</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="csvmodal-title" id="staticBackdropLabel1">Upload CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="upload_response"></div>
                <row>
                    <div class="col">
                        <a href="{{ url('/public/csvsample/OT Allocation.csv') }}" class="control-label d-flex justify-content-end" >CSV Format-Download Sample File</a>
                    </div>
                </row>
                <form method="post" id="formUpload" class="form-horizontal">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-row mb-1">
                                <div class="col-6">
                                        <label class="small font-weight-bold text-dark">CSV File</label>
                                        <input required type="file" id="csv_file_u" name="csv_file_u" class="form-control form-control-sm" accept=".csv"/>
                                </div>                            
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="loading"></div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mt-3">
                                <button type="submit" name="action_button" id="btn-upload" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-upload"></i>&nbsp;Upload </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm closebtn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="ok_button2" id="ok_button2"
                        class="btn btn-danger px-3 btn-sm">OK</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm closebtn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="aviewmodal-title mb-0" id="staticBackdropLabel">
                    <i class="fas fa-calendar-check mr-2"></i>Employee OT Allocation Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0"><i class="fas fa-info-circle mr-2 text-primary"></i>Allocation Summary</h6>
                    </div>
                    <div class="card-body py-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex mb-2">
                                    <span class="font-weight-bold text-muted mr-2" style="width: 100px;">Date:</span>
                                    <span id="view_date" class="text-dark"></span>
                                </div>
                                <div class="d-flex mb-2">
                                    <span class="font-weight-bold text-muted mr-2" style="width: 100px;">Time Slot:</span>
                                    <span id="view_time_slot" class="text-dark"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- <div class="d-flex mb-2">
                                    <span class="font-weight-bold text-muted mr-2" style="width: 100px;">Transport:</span>
                                    <span id="view_transport_badge" class="badge badge-info"></span>
                                </div>
                                <div class="d-flex mb-2">
                                    <span class="font-weight-bold text-muted mr-2" style="width: 100px;">Meal:</span>
                                    <span id="view_meal_badge" class="badge badge-warning text-dark"></span>
                                </div> -->
                                <div class="d-flex">
                                    <span class="font-weight-bold text-muted mr-2" style="width: 100px;">Status:</span>
                                    <span id="view_status_badge" class="badge badge-success"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0"><i class="fas fa-users mr-2 text-primary"></i>Allocated Employees</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-striped table-hover table-sm small mb-0">
                                <thead class="bg-light position-sticky" style="top: 0; z-index: 1;">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 15%;">Employee ID</th>
                                        <th style="width: 20%;">Name</th>
                                        <th style="width: 15%;">Route</th>
                                        <th style="width: 15%;">Vehicle</th>
                                        <th style="width: 15%;">Time Slot</th>
                                        <th style="width: 15%;">Benefits</th>
                                    </tr>
                                </thead>
                                <tbody id="view_tableorderlist"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Area End -->
</main>

@endsection


@section('script')

<script>
    $(document).ready(function () {

    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#employeeot_link').addClass('navbtnactive');

        $('#viewconfirmModal .close').click(function(){
            $('#viewconfirmModal').modal('hide');
        });

        $('#confirmModal2 .close').click(function(){
            $('#confirmModal2').modal('hide');
        });
        $('#confirmModal2 .closebtn').click(function(){
            $('#confirmModal2').modal('hide');
        });

        let employee = $('#employee');
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

        $('#employee').on('select2:select', function (e) {
            var data = e.params.data;
            var routeId = data.route_id;
            if ($('#route option[value="' + routeId + '"]').length) {
                $('#route').val(routeId).trigger('change');
            } else {
                $('#route').val('').trigger('change');
            }
        });

        $('#employee').on('select2:clear', function () {
            $('#route').val('').trigger('change');
        });

        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{!! route('employeeotlist') !!}",

            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<div style="text-align: right;">' + data + '</div>';
                    }
                },
            ],
            "bDestroy": true,
            "order": [
                [0, "desc"]
            ]
        });

        $('#create_record').click(function () {
            $('.modal-title').text('Add OT Allocation');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            
            $('#transport').prop('checked', false);
            $('#meal').prop('checked', false);
            $('#route').val('').trigger('change');
            $('#vehicle').val('').trigger('change');
            
            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i>  Create');
            $('#tableorder > tbody').html('');
            $('#formModal').modal('show');
        });

        $("#formsubmit").click(function () {
            let selectedData = $('#employee').select2('data');
            let date = $('#date').val();
            let time_from = $('#time_from').val();
            let time_to = $('#time_to').val();
            let transport = $('#transport').is(':checked') ? 1 : 0;
            let meal = $('#meal').is(':checked') ? 1 : 0;
            let route = $('#route').val() || 0;
            let vehicle = $('#vehicle').val() || 0;

            if (!selectedData || selectedData.length === 0) {  
                alert('Please select at least one employee.');
                return;
            }
            
            selectedData.forEach(emp => {
                let empId = emp.id;
                let empName = emp.text;
                $('#tableorder > tbody:last').append('<tr class="pointer">' +
                    '<td>' + empId + '</td>' +
                    '<td>' + empName + '</td>' +
                    '<td>' + date + '</td>' +
                    
                    '<td class="d-none">' + time_from + '</td>' +
                    '<td class="d-none">' + time_to + '</td>' +
                    '<td class="d-none">' + transport + '</td>' +
                    '<td class="d-none">' + meal + '</td>' +
                    '<td class="d-none">' + route + '</td>' +
                    '<td class="d-none">' + vehicle + '</td>' +
                    
                    '<td class="text-right"><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>' +
                    '</tr>');
            });
            
            $('#employee').val('').trigger('change');
        });

        $('#btncreateorder').click(function () {
            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('employeeotinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('employeeotupdate') }}";
            }

            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating');

            var tbody = $("#tableorder tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                $("#tableorder tbody tr").each(function () {
                    var item = {};
                    $(this).find('td').each(function (col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });

                var date = $('#date').val();
                var time_from = $('#time_from').val();
                var time_to = $('#time_to').val();
                var transport = $('#transport').is(':checked') ? 1 : 0;
                var meal = $('#meal').is(':checked') ? 1 : 0;
                var route = $('#route').val() || 0;  
                var vehicle = $('#vehicle').val() || 0;  
                var hidden_id = $('#hidden_id').val();

                // Combine date with time to create datetime strings
                var datetime_from = date + ' ' + time_from;
                var datetime_to = date + ' ' + time_to;

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        date: date,
                        time_from: datetime_from,  
                        time_to: datetime_to,
                        transport: transport,
                        meal: meal, 
                        route: route,
                        vehicle: vehicle,    
                        hidden_id: hidden_id,
                    },
                    url: action_url,
                    success: function (data) {
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success +
                                '</div>';
                            $('#formTitle')[0].reset();
                            $('#tableorder tbody').empty();
                            $('#dataTable').DataTable().ajax.reload();
                            setTimeout(function(){
                                $('#formModal').modal('hide');
                            }, 2000);
                            $('#btncreateorder').prop('disabled', false).html(
                                '<i class="fas fa-plus mr-2"></i> Create');
                        }

                        $('#form_result').html(html);
                    }
                });
            } else {
                alert('Cannot Create..Table Empty!!');
                $('#btncreateorder').prop('disabled', false).html(
                '<i class="fas fa-plus mr-2"></i> Create');
            }
        });

        
        // edit function
        $(document).on('click', '.edit', function () {
            var id = $(this).attr('id');

            $('#form_result').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{!! route("employeeotedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#date').val(data.result.mainData.date); 
                    $('#time_from').val(moment(data.result.mainData.time_from).format('HH:mm')); 
                    $('#time_to').val(moment(data.result.mainData.time_to).format('HH:mm'));
                    $('#transport').prop('checked', data.result.mainData.transport == 1);
                    $('#meal').prop('checked', data.result.mainData.meal == 1);
                    $('#route').val(data.result.mainData.route_id || '').trigger('change');
                    $('#vehicle').val(data.result.mainData.vehicle_id || '').trigger('change');
                    $('#tableorderlist').html(data.result.requestdata);

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit OT Allocation');
                    $('#btncreateorder').html('Update Request');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            });
        });

        // request detail edit
        $(document).on('click', '.btnEditlist', function () {
            var id = $(this).attr('id');
            $('#employee').val('').trigger('change');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{!! route("employeeoteditdetails") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#employee').val(data.result.emp_id).trigger('change');
                    $('#time_from').val(data.result.time_from);
                    $('#time_to').val(data.result.time_to);
                    $('#transport').prop('checked', data.result.transport == 1);
                    $('#meal').prop('checked', data.result.meal == 1);
                    $('#route').val(data.result.route_id || '').trigger('change');
                    $('#vehicle').val(data.result.vehicle_id || '').trigger('change');
                    $('#detailsid').val(data.result.id);
                    $('#Btnupdatelist').show();
                    $('#formsubmit').hide();
                }
            });
        });

        // request detail update list
        $(document).on("click", "#Btnupdatelist", function () {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedOption = $('#employee option:selected');
                var employeename = selectedOption.text();
                var detailid = $('#detailsid').val();
                var time_from = $('#time_from').val();
                var time_to = $('#time_to').val();
                var transport = $('#transport').is(':checked') ? 1 : 0;
                var meal = $('#meal').is(':checked') ? 1 : 0;
                var route = $('#route').val() || 0;
                var vehicle = $('#vehicle').val() || 0;

                $("#tableorder> tbody").find('input[name="hiddenid"]').each(function () {
                    var hiddenid = $(this).val();
                    if (hiddenid == detailid) {
                        $(this).parents("tr").remove();
                    }
                });

                $('#tableorder> tbody:last').append('<tr class="pointer">' +
                    '<td>' + emp_id + '</td>' +
                    '<td>' + employeename + '</td>' +
                    '<td>' + time_from + ' - ' + time_to + '</td>' +
                    // Hidden columns
                    '<td class="d-none">' + time_from + '</td>' +
                    '<td class="d-none">' + time_to + '</td>' +
                    '<td class="d-none">' + transport + '</td>' +
                    '<td class="d-none">' + meal + '</td>' +
                    '<td class="d-none">' + route + '</td>' +
                    '<td class="d-none">' + vehicle + '</td>' +
                    '<td class="d-none">Updated</td>' +
                    '<td class="d-none">' + detailid + '</td>' +
                    '<td class="text-right"><button type="button" id="'+detailid+'" class="btnEditlist btn btn-primary btn-sm">' +
                    '<i class="fas fa-pen"></i></button>&nbsp;' +
                    '<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">' +
                    '<i class="fas fa-trash-alt"></i></button></td>' +
                    '<td class="d-none"><input type="hidden" id="hiddenid" name="hiddenid" value="'+detailid+'"></td>' +
                    '</tr>'
                );

                $('#employee').val('').trigger('change');
                $('#Btnupdatelist').hide();
                $('#formsubmit').show();
            }
        });

        //details delete
        var rowid
        $(document).on('click', '.btnDeletelist', function () {
            rowid = $(this).attr('rowid');
            $('#confirmModal2').modal('show');

        });

        $('#ok_button2').click(function () {

            $('#form_result').html('');
            productDelete(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeotdeletelist") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: rowid
                },
                beforeSend: function () {
                    $('#ok_button2').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal2').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        // alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

        $('#csv_upload').click(function () {
                $('#uploadAtModal').modal('show');
                $('#upload_response').html('');
        });
        $('#formUpload').on('submit', function(e) {
            e.preventDefault();
            let save_btn = $("#btn-upload");
            let btn_prev_text = save_btn.html();
            
            save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...');
            let formData = new FormData($('#formUpload')[0]);
            
            $.ajax({
                url: '{{ url("/ot_allocate_csv") }}',
                type: 'POST',
                contentType: false,
                processData: false,
                data: formData,
                success: function(res) {
                    if (res.status) {
                        let successHtml = `<div class='alert alert-success'>${res.msg}</div>`;
                        
                        if (res.errors && res.errors.length > 0) {
                            let errorHtml = '<div class="alert alert-warning mt-2"><strong>Some issues occurred:</strong><ul>';
                            res.errors.forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                            errorHtml += '</ul></div>';
                            successHtml += errorHtml;
                        }
                        
                        $('#upload_response').html(successHtml);
                        
                        if (!res.errors || res.errors.length === 0) {
                            $("#formUpload")[0].reset();
                            setTimeout(function() {
                                $('#uploadAtModal').modal('hide');
                            }, 2000);
                        }
                    } else {
                        let html = '<div class="alert alert-danger">';
                        if (res.errors) {
                            html += '<strong>Errors occurred:</strong><ul>';
                            res.errors.forEach(error => {
                                html += `<li>${error}</li>`;
                            });
                            html += '</ul>';
                        } else {
                            html += res.msg || 'Something went wrong. Please check your file.';
                        }
                        html += '</div>';
                        $('#upload_response').html(html);
                    }
                    
                    save_btn.html(btn_prev_text);
                    $('#uploadAtModal').scrollTop(0);
                    $('#dataTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong. Please check your file.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $('#upload_response').html(`<div class="alert alert-danger">${errorMessage}</div>`);
                    save_btn.html(btn_prev_text);
                }
            });
        });

        var user_id;

        $(document).on('click', '.delete', function () {
            user_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: '{!! route("employeeotdelete") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: user_id
                },
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) { //alert(data);
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        // alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

        // view modal 
        $(document).on('click', '.view', function () {
            id = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeotview") !!}',
                type: 'POST',
                dataType: "json",
                data: { id: id },
                success: function (data) {
                    // Main allocation info
                    $('#view_date').text(data.result.mainData.date);
                    
                    const timeFrom = moment(data.result.mainData.time_from).format('HH:mm');
                    const timeTo = moment(data.result.mainData.time_to).format('HH:mm');
                    $('#view_time_slot').text(`${timeFrom} - ${timeTo}`);
                    
                    // Transport badge
                    const transportBadge = $('#view_transport_badge');
                    transportBadge.text(data.result.mainData.transport ? 'Yes' : 'No');
                    transportBadge.removeClass('badge-info badge-warning')
                                .addClass(data.result.mainData.transport ? 'badge-info' : 'badge-warning text-dark');
                    
                    // Meal badge
                    const mealBadge = $('#view_meal_badge');
                    mealBadge.text(data.result.mainData.meal ? 'Yes' : 'No');
                    mealBadge.removeClass('badge-info badge-warning')
                            .addClass(data.result.mainData.meal ? 'badge-info' : 'badge-warning text-dark');
                    
                    // Status badge
                    const statusBadge = $('#view_status_badge');
                    statusBadge.text(data.result.mainData.status ? 'Active' : 'Inactive');
                    statusBadge.removeClass('badge-success badge-secondary')
                            .addClass(data.result.mainData.status ? 'badge-success' : 'badge-secondary');
                    
                    // Employee table
                    $('#view_tableorderlist').html(data.result.requestdata);

                    $('#viewconfirmModal').modal('show');
                }
            });
        });

        $('#csv_sample').click(function () {
            // var csvContent = "data:text/csv;charset=utf-8," +
            //                 "emp_id,emp_name,date_from,date_to\n" +
            //                 "1,John Doe,2024-08-01,2024-08-31\n" +
            //                 "2,Jane Smith,2024-08-01,2024-08-31";

        });
    });

    function productDelete(row) {
        $(row).closest('tr').remove();
    }

    function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
</script>

<!-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date().toISOString().split('T')[0]; 
        let dateInput = document.getElementById("date");

        dateInput.setAttribute("max", today);
        dateInput.setAttribute("min", today);

        dateInput.addEventListener("change", function () {
            if (this.value !== today) {
                alert("You can only select today's date!");
                this.value = today;
            }
        });
    });
</script> -->

@endsection