
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @can('Employee-Lines-create')
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Employee To Lines</button>
                                <button type="button" class="btn btn-outline-success btn-sm fa-pull-right mr-2" name="csv_upload"
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
                                                <th>Department</th>
                                                <th>Line</th>
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
    </main>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Employee To Lines</h5>
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
                                    <div class="col-3">
                                        <label class="small font-weight-bold text-dark">Department</label>
                                        <select name="department" id="department" class="form-control form-control-sm" style="width: 100%;" required>
                                            <option value="">Select Department</option>
                                            @foreach($department as $departments)
                                            <option value="{{$departments->id}}">{{$departments->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="small font-weight-bold text-dark">Line</label>
                                        <select name="departmentline" id="departmentline" class="form-control form-control-sm" style="width: 100%;" required>
                                            <option value="">Select Line</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" class="form-control form-control-sm"  name="linedate" id="linedate">
                                    </div>
                                    <div class="col-3">
                                        <button style="margin-top:30px;" type="button" name="searchbtn" id="searchbtn"
                                            class="btn btn-primary btn-sm "><i class="fas fa-search"></i>&nbsp;Search</button>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Employee</label>
                                        <select class="employee form-control form-control-sm" id="employee" style="width:100%"></select>
                                    </div>

                                    <div class="col-4">
                                        <button type="button" id="addtolist" class="btn btn-primary btn-sm px-4" style="margin-top:30px;"><i class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    </div>
                                </div>
                                
                                <br>
                                <div class="col-11">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Emp ID</th>
                                                <th>Empolyee Name</th>
                                                <th style="white-space: nowrap;">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody id="emplistbody">
                                        </tbody>
                                    </table>
                                </div>
                                
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

    <div class="modal fade" id="formModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Employee  Line</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result2"></span>
                            <form method="post" id="formTitleedit" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Employee</label>
                                        <select name="editemployee" id="editemployee" class="form-control form-control-sm" style="width:100%">
                                            <option value="">Select Employees</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Line</label>
                                        <select name="editdepartmentline" id="editdepartmentline" class="form-control form-control-sm" style="width: 100%;" required>
                                            <option value="">Select Line</option>
                                            @foreach($departmentline as $departmentlines)
                                            <option value="{{$departmentlines->id}}">{{$departmentlines->line}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" class="form-control form-control-sm"  name="editlinedate" id="editlinedate">
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_buttonedit" id="action_buttonedit"
                                        class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                            class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="1" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
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
                            <a href="{{ url('/csvsample/Employee Line.csv') }}"
                                class="control-label d-flex justify-content-end">CSV Format-Download Sample File</a>
                        </div>
                    </row>
                    <form method="post" id="formUpload" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">CSV File</label>
                                        <input required type="file" id="csv_file_u" name="csv_file_u"
                                            class="form-control form-control-sm" accept=".csv" />
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
                                    <button type="submit" name="action_button" id="btn-upload"
                                        class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                            class="fas fa-upload"></i>&nbsp;Upload </button>
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

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#employeelines').addClass('navbtnactive');

            let employee = $('#employee');

            $('#create_record').click(function(){
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();
                $('#formModal').modal('show');
            });

            $('#csv_upload').click(function () {
                $('#uploadAtModal').modal('show');
                $('#upload_response').html('');
             });

            employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_from_attendance_sel2")}}',
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

           
            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                ajax: {
                    url: scripturl + '/employeedepartmentlineslist.php',

                    type: "POST",

                },
                dom: "<'row'<'col-sm-5'B><'col-sm-2'l><'col-sm-5'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All'],
                ],
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Employee Lines Details',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    {
                        extend: 'print',
                        title: 'Employee Lines Details',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function (win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                "order": [
                    [0, "desc"]
                ],
                "columns": [{
                        "data": "id",
                        "className": 'text-dark'
                    },
                    {
                        "data": "empname",
                        "className": 'text-dark'
                    },
                    {
                        "data": "departmentname",
                        "className": 'text-dark'
                    },
                    {
                        "data": "linename",
                        "className": 'text-dark'
                    },
                    {
                        "data": "date",
                        "className": 'text-dark'
                    },
                    {
                        "targets": -1,
                        "className": 'text-right',
                        "data": null,
                        "render": function (data, type, full) {

                            var button = '';
                        
                                button += ' <button name="edit" id="' + full['id'] + '" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                        
                                button += ' <button name="delete" id="' + full['id'] + '" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                            
                            return button;
                        }
                    }
                ],
                drawCallback: function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
           });


            $('#formTitle').on('submit', function (event) {
                event.preventDefault();

                var tbody = $("#allocationtbl tbody");
                    if (tbody.children().length > 0) {
                        var jsonObj = [];
                        $("#allocationtbl tbody tr").each(function () {
                            var item = {};

                            var empId = $(this).data('empid');
                            item = {
                                'col_1': empId,
                            };
                            
                            jsonObj.push(item);
                        });
                    

                    var departmentline = $('#departmentline').val();
                    var linedate = $('#linedate').val();

                    $.ajax({
                    url: '{!! route("employeedepartmentlinesinsert") !!}',
                    method: 'POST',
                    data: {
                            _token: '{{ csrf_token() }}',
                            tableData: jsonObj,
                            departmentline: departmentline,
                            linedate: linedate
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

                }
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
                    url: '{!! route("employeedepartmentlinesedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#editdepartmentline').val(data.result.line_id);
                        $('#editlinedate').val(data.result.date);
                        $('#hidden_id').val(id);
  
                        var employeeSelect = $('#editemployee');
                        employeeSelect.empty();

                        employeeSelect.append($('<option>', {
                            value: '',
                            text: 'Select Employees'
                        }));

                        if (data.result.emp_id) {
                            employeeSelect.append($('<option>', {
                                value: data.result.emp_id,
                                text: data.result.emp_name,
                                selected: true
                            }));
                        }
                        employeeSelect.prop('disabled', true);

                        $('#action_buttonedit').html('Update');
                        $('#action').val('2');
                        $('#formModal2').modal('show');
                    }
                })
            });

            $('#formTitleedit').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('employeedepartmentlinesupdate') }}";

                    var editdepartmentline = $('#editdepartmentline').val();
                    var editemployee = $('#editemployee').val();
                    var editlinedate = $('#editlinedate').val();
                    var hidden_id = $('#hidden_id').val();

                    $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            editemployee: editemployee,
                            editdepartmentline: editdepartmentline,
                            editlinedate: editlinedate,
                            hidden_id: hidden_id,
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
                            $('#formTitleedit')[0].reset();
                            location.reload()
                        }
                        $('#form_result2').html(html);
                    }
                });
            });

            $("#allocationtbl").on("click", ".addRowBtn", function() {
                
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
                    url: '{!! route("employeedepartmentlinesdelete") !!}',
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


            $('#department').change(function() {
                var departmentId = $(this).val();
                $('#departmentline').html('<option value="">Select Line</option>');
                
                if (departmentId) {
                    $.ajax({
                        url: '{!! route("getlines") !!}',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            department_id: departmentId,
                            _token: '{{ csrf_token() }}' 
                        },
                        success: function(data) {
                            $.each(data, function(key, value) {
                                $('#departmentline').append('<option value="'+ value.id +'">'+ value.line +'</option>');
                            });
                            $('#departmentline').trigger('change');
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });

            $('#searchbtn').click(function () {
                   var departmentline = $('#departmentline').val();
                    var linedate = $('#linedate').val();
                    
                    $.ajax({
                        method: "POST",
                        dataType: "json",
                        data: {
                            _token: '{{ csrf_token() }}',
                            departmentline: departmentline,
                            linedate: linedate,
                        },
                        url: '{!! route("departmentlinesgetemplist") !!}',
                        success: function (data) {
                           
                            var tblemployee = data.result;
                       $("#allocationtbl").prepend(tblemployee);
                        }
                    });


            });


            $('#formUpload').on('submit',function(e) {
                e.preventDefault();
                let save_btn=$("#btn-upload");
                let btn_prev_text = save_btn.html();
               
      
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                let formData = new FormData($('#formUpload')[0]);
                

                let url_text = '{{ url("/departmentlinesemployeeline_csv") }}';
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
                            location.reload()

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
  
            $('#addtolist').click(function () {
                const employeeSelect = $('#employee');
                const selectedOption = employeeSelect.find('option:selected');

                // Check if an employee is selected
                if (selectedOption.val() === '') {
                    alert('Please select an employee');
                    return;
                }

                const empId = selectedOption.val();
                const empName = selectedOption.text();

                // Check if employee already exists in the table
                const existingRow = $(`#emplistbody tr[data-empid="${empId}"]`);
                if (existingRow.length > 0) {
                    alert('This employee is already in the list');
                    return;
                }

                // Add new row to the table
                const newRow = `
                    <tr data-empid="${empId}">
                        <td>${empId}</td>
                        <td>${empName}</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-row">
                                <i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;

                $('#emplistbody').append(newRow);

                employeeSelect.val('');
            });


    $('#allocationtbl').on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
    });

        });

          function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
    function productDelete(ctl) {
    	$(ctl).parents("tr").remove();
    }
    </script>


@endsection

