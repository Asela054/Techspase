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
                <div class="row">
                    <div class="col-12">
                        <div id="form_result"></div>
                    </div>
                </div>
            </div>
        </div>
       
    </div>

</main>
              
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

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        var closedate = $('#closedate').val();
        var company_id = $('#company').val();
        var department_id = $('#department').val();
        $('#btn-filter').html('<i class="fa fa-spinner fa-spin mr-2"></i> Mark Late Attendance').prop('disabled', true);

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
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                    }
                    $('#form_result').html(html);
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 3000);
                }
            });
     
    });



});


</script>

@endsection