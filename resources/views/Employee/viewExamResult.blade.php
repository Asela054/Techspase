@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            @include('layouts.employee_nav_bar')
        </div>
    </div>
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <span id="form_result"></span>
                    <div class="col-9">
                        <div class="container-fluid mt-3">
                      
                        <div class="row">
                            <div class="col-4">
                                
                                <form method="post" id="formTitle" class="form-horizontal">
                                 <input type="hidden" class="form-control form-control-sm" id="empid" name="empid" value="{{$id}}">
                                    {{ csrf_field() }}
                                    <div class="form-row mb-1">
                                        <label class="small font-weight-bold text-dark">Exam Type</label>
                                        <select name="examtype" id="examtype" class="form-control form-control-sm" required>
                                            <option value="">Select Exam Type</option>
                                            <option value="O/L">O/L</option>
                                            <option value="A/L">A/L</option>
                                        </select>
                                    </div>
                                    <div class="form-row mb-1">
                                     <label class="small font-weight-bold text-dark">Subject</label>
                                     <select name="subject" id="subject" class="form-control form-control-sm" required>
                                         <option value="">Select Subject</option>
                                         @foreach($examsubject as $examsubjects)
                                         <option value="{{$examsubjects->id}}">{{$examsubjects->exam_type}} - {{$examsubjects->subject}}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                    <div class="form-row mb-1">
                                            <label class="small font-weight-bold text-dark">Grade*</label>
                                            <input type="text" name="grade" id="grade" class="form-control form-control-sm" required/>
                                    </div>
                                    <div class="form-group mt-3">
                                     <button type="button" id="formsubmit"
                                         class="btn btn-primary btn-sm px-4 float-right"><i
                                             class="fas fa-plus"></i>&nbsp;Add to list</button>
                                     <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                 </div>
                                </form>
                            </div>
                            <div class="col-8">
                             <table class="table table-striped table-bordered table-sm small" id="tableresult">
                                 <thead>
                                     <tr>
                                         <th>Subject</th>
                                         <th>Grade</th>
                                         <th class="d-none">SubjectID</th>
                                     </tr>
                                 </thead>
                                 <tbody></tbody>
                             </table>
                             <div class="form-group mt-2">
                                 <button type="button" name="btncreateorder" id="btncreateorder"
                                     class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                         class="fas fa-plus"></i>&nbsp;Save</button>
                             </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <br>
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display"
                                    style="width: 100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>ID </th>
                                            <th>Exam</th>
                                            <th>Subject</th>
                                            <th>Grade</th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @include('layouts.employeeRightBar')
                </div>
            </div>
        </div>
    </div>
</main>


{{-- EDIT MODEL --}}
<div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg">
   <div class="modal-content">
       <div class="modal-header p-2">
           <h5 class="modal-title" id="staticBackdropLabel">Edit Exam Results</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
           </button>
       </div>
       <div class="modal-body">
        <form method="post" id="formTitleEDIT" class="form-horizontal">
            <div class="row">
                <div class="col">
                  
                        <input type="hidden" name="hidden_id" id="hidden_id" />
                        <div class="row">
                            <div class="col-4">
                                <label class="small font-weight-bold text-dark">Exam Type</label>
                                <select name="eeditxamtype" id="eeditxamtype" class="form-control form-control-sm" required>
                                    <option value="">Select Exam Type</option>
                                    <option value="O/L">O/L</option>
                                    <option value="A/L">A/L</option>
                                </select>
                            </div>
                            <div class="col-4">
                             <label class="small font-weight-bold text-dark">Subject</label>
                             <select name="editsubject" id="editsubject" class="form-control form-control-sm" required>
                                 <option value="">Select Subject</option>
                                 @foreach($examsubject as $examsubjects)
                                 <option value="{{$examsubjects->id}}">{{$examsubjects->exam_type}} - {{$examsubjects->subject}}</option>
                                 @endforeach
                             </select>
                         </div>
                            <div class="col-4">
                                    <label class="small font-weight-bold text-dark">Grade*</label>
                                    <input type="text" name="editgrade" id="editgrade" class="form-control form-control-sm" required/>
                            </div>
                        </div>    
                </div>
            
            </div>
               
               <div class="form-group mt-3">
                <button type="submit" name="action_button" id="action_button" 
                class="btn btn-outline-primary btn-sm fa-pull-right px-4">
                    <i class="fas fa-plus"></i>&nbsp;Update</button>

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
                <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
              
@endsection

@section('script')
<script>
  $(document).ready(function(){
    $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
        $('#view_examresult_link').addClass('navbtnactive');

   $("#subject").select2();

   $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! route('examresultlist') !!}",
                type: "POST",
                data: { 
                    _token: '{{ csrf_token() }}',
                    'empid': $('#empid').val() },
            },
            columns: [{
                    data: 'resultid',
                    name: 'resultid'
                }, 
                {
                    data: 'exam_type',
                    name: 'exam_type'
                }, 
                {
                    data: 'subjectname',
                    name: 'subjectname'
                },
                {
                    data: 'grade',
                    name: 'grade'
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
                [0, "asc"]
            ]
        });


 // add to tempery table

    $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();
            } else {
                var examtype = $('#examtype').val();
                var SubjectID = $('#subject').val();
                var grade = $('#grade').val();
                
                var subject = $("#subject option:selected").text();
               

                $('#tableresult > tbody:last').append('<tr class="pointer"><td>' + subject +
                    '</td><td>' + grade + '</td><td class="d-none">' + SubjectID +
                    '</td></tr>');

                $('#subject').val('');
                $('#grade').val('');
            }
        });

// tempery table record delete function

        $('#tableresult').on('click', 'tr', function () {
            var r = confirm("Are you sure, You want to remove this Result ? ");
            if (r == true) {
                $(this).closest('tr').remove();
            }
        });

// data insert function

        $('#btncreateorder').click(function () {
            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Save');

            var tbody = $("#tableresult tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                $("#tableresult tbody tr").each(function () {
                    var item = {};
                    $(this).find('td').each(function (col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });

                var empid = $('#empid').val();
                var examtype = $('#examtype').val();


                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        empid: empid,
                        examtype: examtype,

                    },
                    url: "{{ route('examresultinsert') }}",
                    success: function (result) {
                        if (result.status == 1) {
                            setTimeout(function () {
                                location.reload();
                            }, 100);
                        }
                        action(result.action);
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
                    url: '{!! route("examresultedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#eeditxamtype').val(data.result.exam_type);
                        $('#editsubject').val(data.result.subject_id);
                        $('#editgrade').val(data.result.grade);
                        $('#hidden_id').val(id);
                        $('#formModal').modal('show');
                    }
                })
            });


            $('#formTitleEDIT').on('submit', function(event){
                event.preventDefault();
                $.ajax({
                    url:'{!! route("examresultupdate") !!}',
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (data) {//alert(data);
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
                            $('#formTitleEDIT')[0].reset();
                            location.reload()
                        }
                        $('#form_result').html(html);
                    }
                });
            });

            // delete model function
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
                    url: '{!! route("examresultdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {//alert(data);
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#dataTable').DataTable().ajax.reload();
                            alert('Data Deleted');
                        }, 2000);
                        location.reload()
                    }
                })
            });
  });

</script>
@endsection
