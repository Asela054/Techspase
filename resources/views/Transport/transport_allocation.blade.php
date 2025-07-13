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
                        @can('transport-allocation-create')
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right mr-2" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add Allocation</button>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Add Transport Allocation</h5>
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
                                        <input type="date" name="date" id="date" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-6">
                                    <label class="small font-weight-bold text-dark">Route</label>
                                        <select id="route" name="route" class="form-control form-control-sm" required>
                                            <option value="">Select Route</option>
                                            @foreach ($transportroute as $transportroutes){
                                                <option value="{{$transportroutes->id}}" >{{$transportroutes->name}}</option>
                                            }  
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                    <label class="small font-weight-bold text-dark">Vehicle</label>
                                        <select id="vehicle" name="vehicle" class="form-control form-control-sm" required>
                                            <option value="">Select Vehicle</option>
                                            @foreach ($transportvehicle as $transportvehicles){
                                                <option value="{{$transportvehicles->id}}" >{{$transportvehicles->vehicle_number}}</option>
                                            }  
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1" id="employee-container" style="display: none;">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select name="employee[]" id="employee" class="form-control form-control-sm" multiple required></select>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="col-6">
                                    <button type="button" id="formsubmit"
                                        class="btn btn-primary btn-sm px-4 float-right"><i
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
                                        <th>Route</th>
                                        <th>Vehicle</th>
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
            <div class="modal-header p-2">
                <h5 class="aviewmodal-title" id="staticBackdropLabel">View Transport Allocation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-row mb-1">
                                <div class="col-6">
                                    <label class="small font-weight-bold text-dark">Date*</label>
                                    <input type="date" name="view_date" id="view_date" class="form-control form-control-sm" required readonly style="pointer-events: none"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Employee Name</th>
                                        <th>Route</th>
                                        <th>Vehicle</th>
                                    </tr>
                                </thead>
                                <tbody id="view_tableorderlist"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                </form>
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
    $('#transport_link').addClass('navbtnactive');

        $('#viewconfirmModal .close').click(function(){
            $('#viewconfirmModal').modal('hide');
        });

        $('#confirmModal2 .close').click(function(){
            $('#confirmModal2').modal('hide');
        });
        $('#confirmModal2 .closebtn').click(function(){
            $('#confirmModal2').modal('hide');
        });

        let employee = $("#employee").select2({
            placeholder: "Select employees...",
            width: "100%",
            allowClear: true,
            multiple: true,
            ajax: {
                url: '{{url("employee_list_for_transport")}}',
                dataType: "json",
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        date: $('#date').val() 
                    }
                },
                cache: true
            }
        });  

        $('#date').on('change', function() {
            if ($(this).val()) {
                $('#employee-container').show();
                $('#employee').prop('disabled', false);
                $('#employee').val(null).trigger('change'); 
            } else {
                $('#employee-container').hide();
                $('#employee').prop('disabled', true);
                $('#employee').val(null).trigger('change'); 
            }
        });

        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{!! route('TransportAllocationlist') !!}",

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
            $('.modal-title').text('Add Transport Allocation');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i>  Create');
            $('#tableorder > tbody').html('');
            
            $('#formModal').modal('show');
        });


        $("#formsubmit").click(function () {
            let selectedEmployees = $('#employee').val();
            let routeId = $('#route').val();
            let routeName = $('#route option:selected').text();

            let vehicleId = $('#vehicle').val();
            let vehicleNumber = $('#vehicle option:selected').text();


            if (!selectedEmployees || selectedEmployees.length === 0) {
                alert('Please select at least one employee.');
                return;
            }
            
            selectedEmployees.forEach(empId => {
                let empName = $('#employee option[value="' + empId + '"]').text();

                $('#tableorder > tbody:last').append('<tr class="pointer">' +
                    '<td>' + empId + '</td>' +
                    '<td>' + empName + '</td>' +
                    '<td data-id="' + routeId + '">' + routeName + '</td>' +
                    '<td data-id="' + vehicleId + '">' + vehicleNumber + '</td>' +
                    '<td class="text-right"><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>' +
                    '</tr>');
            });
            
            $('#employee').val('').trigger('change');
        });

        $('#btncreateorder').click(function () {
            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('TransportAllocationinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('TransportAllocationupdate') }}";
            }

            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating');

            var tbody = $("#tableorder tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                    $("#tableorder tbody tr").each(function () {
                    var item = {};
                    var tds = $(this).find('td');

                    item["employee_id"] = $(tds[0]).text();
                    item["employee_name"] = $(tds[1]).text();
                    item["route_id"] = $(tds[2]).attr("data-id");
                    item["route_name"] = $(tds[2]).text();
                    item["vehicle_id"] = $(tds[3]).attr("data-id");
                    item["vehicle_number"] = $(tds[3]).text();

                    jsonObj.push(item);
                });
                var date = $('#date').val();
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    tableData: jsonObj,
                    date: date,
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
                url: '{!! route("TransportAllocationedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#date').val(data.result.mainData.date); 
                    $('#tableorderlist').html(data.result.requestdata);

                    // Hide the date column
                    // $('#tableorder th:nth-child(3), #tableorder td:nth-child(3)').hide();

                    // Hide the Edit button in the action column
                    $('#tableorder .btnEditlist').hide();

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Shift Extend Request');
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
            $('#route').val('').trigger('change');
            $('#vehicle').val('').trigger('change');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("TransportAllocationeditdetails") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#employee').val(data.result.emp_id).trigger('change');
                    $('#route').val(data.result.route).trigger('change');
                    $('#vehicle').val(data.result.vehicle).trigger('change');
                    $('#detailsid').val(data.result.id);
                    $('#Btnupdatelist').show();
                    $('#formsubmit').hide();
                }
            })
        });

        // request detail update list
        $(document).on("click", "#Btnupdatelist", function () {
            if (!$("#formTitle")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedOption = $('#employee option:selected');

                var detailid = $('#detailsid').val();
            
            $("#tableorder> tbody").find('input[name="hiddenid"]').each(function () {
                var hiddenid = $(this).val();
                if (hiddenid == detailid) {
                    $(this).parents("tr").remove();
                }
            });

            $('#tableorder> tbody:last').append('<tr class="pointer"><td name="empid">' + emp_id +
                '</td><td name="empname">' + employeename +
                '</td><td class="d-none">Updated</td><td class="d-none">' +
                detailid +
                '</td><td class="text-right"><button type="button" id="'+detailid+'" class="btnEditlist btn btn-primary btn-sm "><i class="fas fa-pen"></i>'+
                '</button>&nbsp;<button type="button" onclick= "productDelete(this);" id="btnDeleterow" class=" btn btn-danger btn-sm "><i class="fas fa-trash-alt"></i></button></td>'+
                '<td class="d-none"><input type ="hidden" id ="hiddenid" name="hiddenid" value="'+detailid+'"></td>'+
                '</tr>'
            );
            $('#employee').val('').trigger('change');
            $('#Btnupdatelist').hide();
            $('#formsubmit').show();
            }
        });

        //   details delete
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
                url: '{!! route("TransportAllocationdeletelist") !!}',
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
                url: '{!! route("TransportAllocationdelete") !!}',
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
                url: '{!! route("TransportAllocationview") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#view_date').val(data.result.mainData.date); 
                    $('#view_tableorderlist').html(data.result.requestdata);

                    $('#viewconfirmModal').modal('show');

                }
            })


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

@endsection