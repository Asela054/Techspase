@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
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
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd"
                                           value="{{date('Y-m-d') }}"
                                           required
                                    >
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd"
                                           value="{{date('Y-m-d') }}"
                                           required
                                    >
                                </div>
                            </div>
                            <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm filter-btn float-right ml-2" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
                            <button type="button" class="btn btn-danger btn-sm filter-btn float-right" id="btn-clear"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Clear</button>
                        </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="info_msg">
                        <div class="alert alert-info" role="alert">
                            <span><i class="fa fa-info-circle"></i>  Records for {{date('Y-m-d')}} showing by default </span>
                        </div>
                    </div>
                    <div class="response">
                    </div>
                    {{ csrf_field() }}
                </div>
            </div>
        </div>

    </main>

@endsection

@section('script')
<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://unpkg.com/jspdf-autotable@3.8.3/dist/jspdf.plugin.autotable.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');

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

            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_from_attendance_sel2")}}',
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

            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            // load_dt('', '', '', from_date, to_date);
            

            document.getElementById('btn-clear').addEventListener('click', function() {
            document.getElementById('formFilter').reset();

                        $('#company').val('').trigger('change');   
                        $('#department').val('').trigger('change');
                        $('#location').val('').trigger('change');
                        $('#employee').val('').trigger('change');
                                        
                        // load_dt('', '', '', '');
            });

            $(document).on('click', '#export_pdf', function () {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                doc.text("Incomplete Attendance Report", 14, 15);
                doc.autoTable({
                    html: '#attendance_report_table',
                    startY: 25,
                    styles: {
                        font: 'Roboto',
                        fontSize: 8
                    },
                    headStyles: {
                        fillColor: [52, 58, 64] 
                    }
                });

                doc.save('Incomplete_attendance_report.pdf');
            });

            $(document).on('click', '#export_excel', function () {
                let table = document.getElementById('attendance_report_table');
                let wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
                XLSX.writeFile(wb, 'Incomplete_attendance_report.xlsx');
            });

            function load_dt(department, employee, location, from_date, to_date) {
                $('.response').html('');

                let filterButton = $('#btn-filter');
                filterButton.attr('disabled', true);
                filterButton.html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "{{ route('get_incomplete_attendance_by_employee_data') }}",
                    method: "POST",
                    data: {
                        department: department,
                        employee: employee,
                        location: location,
                        from_date: from_date,
                        to_date: to_date,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {
                        filterButton.html('<i class="fas fa-search mr-2"></i>Filter');
                        filterButton.prop('disabled', false);

                        $('.response').html(res);
                    }
                });
            }


            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $('.info_msg').html('');

                load_dt(department, employee, location, from_date, to_date);
            });

            //document .excel-btn click event
            $(document).on('click', '#btn_mark_as_no_pay', function(e) {
                e.preventDefault();

                //btn
                let btn = $(this);
                let btn_text = $(this).html();

                let checked = [];
                //each checked checkbox
                $('.checkbox_attendance:checked').each(function() {
                    let element = $(this);

                    let etf_no = $(this).data('etf_no');
                    let date = $(this).data('date');

                    checked.push({
                        etf_no: etf_no,
                        date: date
                    });
                });

                if(checked.length > 0) {
                    $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                    $(btn).prop('disabled', true);

                    $.ajax({
                        url: "{{ route('mark_as_no_pay') }}",
                        method: "POST",
                        data: {
                            checked: checked,
                            _token: '{{csrf_token()}}'
                        },
                        success: function (res) {
                            if(res.success) {
                                $('.info_msg').html('<div class="alert alert-success">' + res.success + '</div>');
                                $('.checkbox_attendance:checked').each(function() {
                                    $(this).parent().parent().remove();
                                });
                            }

                            $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                            $(btn).prop('disabled', true);
                            //scroll to top
                            $('html, body').animate({
                                scrollTop: 100
                            }, 'fast');
                        }
                    });
                } else {
                    $('.info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                    $('html, body').animate({
                        scrollTop: 100
                    }, 'fast');
                }

            });

             $(document).on('click', '#btn_updatesttendace', function() {
                var updatedRecords = [];
                
                $('#attendance_report_table tbody tr').each(function() {
                    // Skip rows that don't have the attendance checkbox
                    if ($(this).find('.checkbox_attendance').length === 0) return;

                    var timeIn = $(this).find('.time_in');
                    var timeOut = $(this).find('.time_out');

                    var originalTimeIn = timeIn.data('timestamp');
                    var originalTimeOut = timeOut.data('timestamp');

                    var currentTimeIn = timeIn.val();
                    var currentTimeOut = timeOut.val();

                    // Check if both timestamps are filled and either one has changed
                    if (currentTimeIn && currentTimeOut && 
                        (currentTimeIn !== originalTimeIn || currentTimeOut !== originalTimeOut)) {
                        
                        var empid = $(this).find('.checkbox_attendance').data('empid');
                        var date = $(this).find('.checkbox_attendance').data('date');

                        updatedRecords.push({
                            emp_id: empid,
                            date: date,
                            timestamp: currentTimeIn,
                            lasttimestamp: currentTimeOut
                        });
                    }
                });

                if (updatedRecords.length === 0) {
                    alert('No changes detected or both timestamp fields are not filled!');
                    return;
                }

                $.ajax({
                    url: '{{ route("update_incomplete_attendace") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        updatedrecords: updatedRecords
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            console.error('Server error:', response.message);
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('AJAX Error: ' + error);
                    }
                });
            });

        });
    </script>

@endsection


