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
                        <div class="col-2">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col-2">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col-2">
                            <label class="small font-weight-bold text-dark">Attendance Date</label>
                            <input type="date" id="closedate" name="closedate" class="form-control form-control-sm" required>
                        </div>
                        <div class="col">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"><i class="fas fa-search mr-2"></i>Mark Late Attendance</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
          

        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                        <div class="col-12">
                            <div class="row align-items-center mb-4">
                                <div class="col-2 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-10 ">
                                    <button id="approve" class="btn btn-primary btn-sm fa-pull-right mr-2">Apply Late Minites</button>
                                </div>
                            </div>
                            
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display"
                                    style="width: 100%" id="dataTable">
                                    <thead>
                                    <tr> <th></th>
                                        <th>Employee ID</th>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Late Minutes</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Working Hours</th>
                                         <th class="d-none">attendace id</th>
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
     

<div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Apply Late Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to Submit this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="approve_button" id="approve_button"
                        class="btn btn-warning px-3 btn-sm">Apply</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
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
    $('#attendantmaster').addClass('navbtnactive');

    let company = $('#company');
    let department = $('#department');

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

   $('#formFilter').on('submit', function (e) {
        e.preventDefault();
        var closedate = $('#closedate').val();
        var company_id = $('#company').val();
        var department_id = $('#department').val();
        $('#btn-filter').html('<i class="fa fa-spinner fa-spin mr-2"></i> Marking Late Attendance').prop('disabled', true);

        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        $.ajax({
            url: "{{url('/lateminitesmarkauto')}}",
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                closedate: closedate,
                company_id: company_id,
                department_id: department_id
            },
            dataType: "json",
            success: function (data) {
                $('#btn-filter').html('<i class="fas fa-search mr-2"></i>Mark Late Attendance').prop('disabled', false);
                var html = '';
                $('#dataTable tbody').empty();



                $.each(data.lateAttendance, function (index, item) {
                    html += `<tr>
                                            <td><input type="checkbox" class="row-checkbox selectCheck removeIt"></td>
                                            <td>${item.emp_id}</td>
                                            <td>${item.emp_name}</td>
                                            <td>${item.attendance_date}</td>
                                            <td>${item.minites_count}</td>
                                            <td>${item.check_in_time}</td>
                                            <td>${item.check_out_time}</td>
                                            <td>${item.working_hours}</td>
                                            <td class= "d-none">${item.attendance_id}</td>
                                        </tr>`;
                });
                $('#dataTable tbody').html(html);
                $('#dataTable').DataTable({
                    responsive: true,
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 1]
                    }]
                });
            }
        });
    });

    var selectedRowIdsapprove = [];

        $('#approve').click(function () {
                    selectedRowIdsapprove = [];
                    $('#dataTable tbody .selectCheck:checked').each(function () {
                        var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();

                        if (rowData) {
                            selectedRowIdsapprove.push({
                                empid: rowData[1],
                                emp_name: rowData[2], 
                                attendacedate: rowData[3],
                                minites_count: rowData[4],
                                check_in_time: rowData[5],
                                check_out_time: rowData[6],
                                working_hours: rowData[7],
                                attendaceid: rowData[8],
                            });
                        }
                    });

                    if (selectedRowIdsapprove.length > 0) {
                     console.log(selectedRowIdsapprove);
                        $('#approveconfirmModal').modal('show');
                    } else {
                        
                        alert('Select Rows to Final Approve!!!!');
                    }
        });

        $('#approve_button').click(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            var closedate = $('#closedate').val();
            var company_id = $('#company').val();
            var department_id = $('#department').val();

            $.ajax({
                url: '{!! route("lateminitesmarkautoapply") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    dataarry: selectedRowIdsapprove,
                     closedate: closedate,
                    company_id: company_id,
                    department_id: department_id
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#approveconfirmModal').modal('hide');
                        location.reload();
                    }, 500);

                    $('#selectAll').prop('checked', false);
                   
                }
            })
        });


  $('#selectAll').click(function (e) {
            $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
  });

});


</script>

@endsection