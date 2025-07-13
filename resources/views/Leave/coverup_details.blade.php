@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.attendant&leave_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-4" style="margin-top:20px;">
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right ml-2" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                                <button type="button" class="btn btn-danger btn-sm filter-btn float-right" id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right"
                                    name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Covering Details
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm fa-pull-right mr-2" name="csv_upload" id="csv_upload"><i class="fas fa-plus mr-2"></i>Add - CSV Upload</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="divicestable">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Cover Date</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Covering Hours</th>
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
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Coverup Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
                                <form method="post" id="formTitle" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Covering Employee</label>
                                            <select name="coveringemployee" id="coveringemployee"
                                                    class="form-control form-control-sm" required>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Cover Date</label>
                                            <input type="date" name="coverdate" id="coverdate" class="form-control form-control-sm" required/>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Date</label>
                                            <input type="date" name="date" id="date" class="form-control form-control-sm" required/>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Start Time</label>
                                            <input type="datetime-local" name="start_time" id="start_time"
                                                   class="form-control form-control-sm" required/>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">End Time</label>
                                            <input type="datetime-local" name="end_time" id="end_time"
                                                   class="form-control form-control-sm" required/>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="submit" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4" value="Add"/>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add"/>
                                    <input type="hidden" name="hidden_id" id="hidden_id"/>
                                </form>
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
                        <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK
                        </button>
                        <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel1" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
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
                        <a href="{{ url('/public/csvsample/Covering_details.csv') }}" class="control-label d-flex justify-content-end" >CSV Format-Download Sample File</a>
                    </div>
                   </row>
                   <form method="post" id="formUpload" class="form-horizontal">
                       {{ csrf_field() }}
                       <div class="row">
                           <div class="col">
                               <div class="form-row mb-1">
                                   <div class="col">
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


        <!-- Modal Area End -->
    </main>

@endsection


@section('script')

<script>
    $(document).ready(function () {
        var canleaveedit = false;
        @can('leave-edit')
            canleaveedit = true;
        @endcan

        var leavedelete = false;
        @can('leave-delete')
            leavedelete = true;
        @endcan
        
        $('#attendant_menu_link').addClass('active');
        $('#attendant_menu_link_icon').addClass('active');
        $('#leavemaster').addClass('navbtnactive');

        $('#csv_upload').click(function () {
            $('#uploadAtModal').modal('show');
            $('#upload_response').html('');
        });

        let company_f = $('#company_f');
        let department_f = $('#department_f');
        let employee_f = $('#employee_f');
        let location_f = $('#location_f');

        company_f.select2({
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

        department_f.select2({
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
                        company: company_f.val()
                    }
                },
                cache: true
            }
        });

        employee_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_sel2")}}',
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

        location_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("location_list_sel2")}}',
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

        let c_employee = $('#coveringemployee');
        c_employee.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            parent: '#formModal',
            ajax: {
                url: '{{url("employee_list_sel2")}}',
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

        function load_dt(department, employee, location){
            $('#divicestable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Print',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    "url": scripturl + "/coverupdetaillist.php",
                    "type": "POST",
                    "data": {'department':department, 'employee':employee, 'location': location},
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                    { data: 'name', name: 'name' },
                    { data: 'coverdate', name: 'coverdate' },
                    { data: 'date', name: 'date' },
                    { data: 'start_time', name: 'start_time' },
                    { data: 'end_time', name: 'end_time' },
                    { data: 'covering_hours', name: 'covering_hours' },
                    {
                        "targets": -1,
                        orderable: false, 
                        searchable: false,
                        "className": 'text-right',
                        "data": null,
                        "render": function(data, type, full) {
                            var buttons = '';

                            if (canleaveedit) {
                                buttons += '<button name="edit" id="'+ full['id'] +'"class="edit btn btn-outline-primary btn-sm" style="margin:1px;" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                            }

                            if (leavedelete) {
                                buttons += '<button type="submit" name="delete" id="'+ full['id'] +'"class="delete btn btn-outline-danger btn-sm" style="margin:1px;" ><i class="far fa-trash-alt"></i></button>';
                            }

                            return buttons;
                        }
                    }
                ],
                "bDestroy": true,
                "order": [
                    [3, "desc"]
                ]
            });
        }

        load_dt('', '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department_f').val();
            let employee = $('#employee_f').val();
            let location = $('#location_f').val();

            load_dt(department, employee, location);
        });

        document.getElementById('btn-clear').addEventListener('click', function() {
            document.getElementById('formFilter').reset();

            $('#company_f').val('').trigger('change');   
            $('#location_f').val('').trigger('change');
            $('#department_f').val('').trigger('change');
            $('#employee_f').val('').trigger('change');

            // load_dt('', '', '', '', '');
        });
    });    

    $(document).ready(function () {
        $('#create_record').click(function () {
            $('.modal-title').text('Covering Details');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            //form reset                
            $('#formTitle')[0].reset();
            $('#coveringemployee').val('').trigger('change');
            $('#date').val('');
            $('#start_time').val('');
            $('#end_time').val('');
            
            $('#formModal').modal('show');
        });

        $('#formTitle').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';


            if ($('#action').val() == 'Add') {
                action_url = "{{ route('addCoverup') }}";
            }

            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('Coverup.update') }}";
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
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
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#formTitle')[0].reset();
                        $('#divicestable').DataTable().ajax.reload();
                        setTimeout(function() { $('#formModal').modal('hide'); }, 1000);
                    }
                    $('#form_result').html(html);
                }
            });
        });

        $(document).on('click', '.edit', function () {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "Coverup/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    let coveringemployeeOption = $("<option selected></option>").val(data.result.emp_id).text(data.result.covering_employee.emp_name_with_initial);
                    $('#coveringemployee').append(coveringemployeeOption).trigger('change');

                    $('#coveringemployee').val(data.result.emp_id);
                    $('#coverdate').val(data.result.coverdate);
                    $('#date').val(data.result.date);
                    $('#start_time').val(data.result.start_time);
                    $('#end_time').val(data.result.end_time);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Covering Details');
                    $('#action_button').val('Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        });

        var user_id;

        $(document).on('click', '.delete', function () {
            user_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function () {
            $.ajax({
                url: "Coverup/destroy/" + user_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#divicestable').DataTable().ajax.reload();
                        alert('Data Deleted');
                    }, 2000);
                    location.reload();
                }
            })
        });

        $('#formUpload').on('submit',function(e) {
            e.preventDefault();
            let save_btn=$("#btn-upload");
            let btn_prev_text = save_btn.html();
            
    
            save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
            let formData = new FormData($('#formUpload')[0]);
            

            let url_text = '{{ url("/coveringcsvupload") }}';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: url_text,
                type: 'POST',
                contentType: false,
                processData: false,
                data: formData,
                success: function(res) {
                    // console.log(res);
                    
                    if (res.status == 1) {
                        $('#upload_response').html("<div class='alert alert-success'>"+res.msg+"</div>");

                        save_btn.html(btn_prev_text);
                        save_btn.prop("disabled", false);
                        $("#formUpload")[0].reset();
                        $('#uploadAtModal').scrollTop(0);
                        $('#divicestable').DataTable().ajax.reload();
                        setTimeout(function(){
                            $('#uploadAtModal').modal('hide');
                        }, 2000);

                    }else {

                        var html = '';
                        if (res.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < res.errors.length; count++) {
                                html +=   res.errors[count]+'<br>' ;
                            }
                            html += '</div>';
                        }
                        $('#upload_response').html(html);
                        save_btn.prop("disabled", false);
                        save_btn.html(btn_prev_text);
                    }
                },
                error: function(res) {
                    alert(data);
                }
            });
        });
    });
</script>

@endsection