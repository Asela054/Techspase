
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
                    <div class="row">
                        <div class="col-12">
                            @can('Job-Attendance-create')
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>New Gate Pass</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm fa-pull-right mr-2" name="csv_upload"
                                id="csv_upload"><i class="fas fa-plus mr-2"></i>Add - CSV Upload</button>
                                @endcan
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-12">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display"
                                        style="width: 100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Employee Name</th>
                                                <th>Date</th>
                                                <th>On Time</th>
                                                <th>Off Time</th>
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

    </main>


            <!-- Modal Area Start -->
            <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header p-2">
                            <h5 class="modal-title" id="staticBackdropLabel">Add Gate Pass</h5>
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
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="small font-weight-bold text-dark">Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="gatedate" id="gatedate" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="small font-weight-bold text-dark">Employee</label>
                                                <select name="employee" id="employee" class="form-control form-control-sm" required>
                                                    <option value="">Please Select</option>
                                                    @foreach ($employees as $employee){
                                                        <option value="{{$employee->id}}">{{$employee->emp_name_with_initial}}</option>
                                                    }  
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="small font-weight-bold text-dark">On  Time</label>
                                                <input type="datetime-local" name="ontime" id="ontime" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" required/>
                                            </div>
                                            <div class="col-6">
                                                <label class="small font-weight-bold text-dark">Off  Time</label>
                                                <input type="datetime-local" name="offtime" id="offtime" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" />
                                            </div>
                                        </div>
                                       <br>
                                       <input type="hidden" name="action" id="action" value="1">
                                       <input type="hidden" name="hidden_id" id="hidden_id" >
                                        <div class="form-group mt-3">
                                            <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                        </div>
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
                            <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK</button>
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
                            <a href="{{ url('/csvsample/Gate Pass.csv') }}" class="control-label d-flex justify-content-end" >CSV Format-Download Sample File</a>
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

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#gatepassmanagement').addClass('navbtnactive');
            
            $('#employee').select2({ width: '100%'});

            $('#create_record').click(function(){
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();
                $('#formModal').modal('show');
                $('#staticBackdropLabel').text('Add Gate Pass');
                $('#action').val('1');
            });

            $('#csv_upload').click(function () {
                $('#uploadAtModal').modal('show');
                $('#upload_response').html('');
        });
            

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('gatepasslist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'emp_name', name: 'emp_name' },
                    { data: 'date', name: 'date' },
                    { data: 'intime', name: 'intime' },
                    { data: 'offtime', name: 'offtime' },
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


            $('#formTitle').on('submit', function (event) {
                event.preventDefault();

                    var gatedate = $('#gatedate').val();
                    var employee = $('#employee').val();
                    var ontime = $('#ontime').val();
                    var offtime = $('#offtime').val();
                    var hidden_id = $('#hidden_id').val();
                    var action = $('#action').val();
                     

                    $.ajax({
                    url: '{!! route("gatepassinsert") !!}',
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            date: gatedate,
                            employeeID: employee,
                            ontime: ontime,
                            offtime: offtime,
                            hidden_id: hidden_id,
                            action:action
                        },
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
                            location.reload()
                        }
                        $('#form_result').html(html);
                    }
                });

            });

            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
               
                $('#form_result').html('');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                $.ajax({
                    url: '{!! route("gatepassedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#gatedate').val(data.result.date);
                        $('#employee').val(data.result.emp_id);
                        $('#ontime').val(data.result.intime);
                        $('#offtime').val(data.result.offtime);
                        $('#hidden_id').val(id);
                        $('#action_button').html('Update');
                        $('#action').val('2');
                        $('#formModal').modal('show');
                        $('#staticBackdropLabel').text('Edit Gate Pass');
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
                    url: '{!! route("gatepassdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload()
                    }
                })
            });


            $('#formUpload').on('submit',function(e) {
                e.preventDefault();
                let save_btn=$("#btn-upload");
                let btn_prev_text = save_btn.html();
               
      
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                let formData = new FormData($('#formUpload')[0]);
                

                let url_text = '{{ url("/gatepasscsvupload") }}';
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
                        if (res.status == 1) {
                            $('#upload_response').html("<div class='alert alert-success'>"+res.msg+"</div>");

                            save_btn.html(btn_prev_text);
                            save_btn.prop("disabled", false);
                            $("#formUpload")[0].reset();
                            $('#uploadAtModal').scrollTop(0);
                            $('#dataTable').DataTable().ajax.reload();
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

