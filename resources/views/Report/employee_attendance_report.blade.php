
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
                    <!-- <form class="form-horizontal" id="formFilter"  method="POST" action="{{ url('employeeattendancereportgenerate') }}"> -->
                    <form class="form-horizontal" id="formFilter">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
    
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                            <div class="col-md-3" >
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <!-- <div class="col-md-3" >
                                <label class="small font-weight-bold text-dark">Paginattion Range</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" id="from_range" name="from_range" class="form-control form-control-sm border-right-0">
                                    <input type="number" id="to_range" name="to_range" class="form-control">
                                </div>
                            </div> -->
                            <div class="col">
                                <br>
                                <button type="button" id="pdf_excel" class="btn btn-sm btn-primary" style="margin-top:5px;"><i class="fas fa-search" ></i>&nbsp;Search</button>
                                <input type="submit" class="d-none" id="hideformsubmit">
                                <button type="button" class="btn btn-danger btn-sm" id="btnexport"><i class="fas fa-file-pdf mr-2"></i>Export PDF</button>
                            </div>
                        </div>

                    </form>
                    <div class="alert alert-primary" role="alert">
                    Employee data now loads in batches. First 100 entries, then scroll to load more.
                    </div>
                    <div id="employee_list"></div>
                </div>
            </div>
    </main>



@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeedetailsreport').addClass('navbtnactive');
            $('#department').select2({ width: '100%' });

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

            // $('#pdf_excel').click(function(){
            //     if (!$("#formFilter")[0].checkValidity()) {
            //         // If the form is invalid, submit it to display HTML5 error messages
            //         $("#hideformsubmit").click();
            //     } else {
            //         var page = 1;
            //         var loading = false;
            //         var department = $('#department').val();
            //         var from_date = $('#from_date').val();
            //         var to_date = $('#to_date').val();

            //         $.ajax({
            //             url: "{{ route('employeeattendancereportgenerate') }}",
            //             method: "POST",
            //             data: {
            //                 department: department,
            //                 from_date: from_date,
            //                 to_date: to_date,
            //                 _token: '{{csrf_token()}}'
            //             },
            //             success: function (result) {
            //                 if (result.length > 0) {
            //                     $.each(result, function (index, employee) {
            //                         $('#employee_list').append(`
            //                             <tr>
            //                                 <td>${employee.employee.emp_id}</td>
            //                                 <td>${employee.employee.emp_fullname}</td>
            //                                 <td>${employee.employee.departmentname}</td>
            //                                 <td>${employee.attendance[0].in_time}</td>
            //                                 <td>${employee.attendance[0].out_time}</td>
            //                                 <td>${employee.attendance[0].day_type}</td>
            //                             </tr>
            //                         `);
            //                     });

            //                     page++; // Increa
            //                 }
            //                 else {
            //                     $(window).off("scroll"); // Stop loading if no more data
            //                 }
            //             }
            //         });
            //     }
            // });
            var loading = false;
            var lastEmpId = 0;

            function loadEmployees() {
                if (loading) return;
                loading = true;
                var departmentID = $('#department').val();
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();            

                $.ajax({
                    url: "{{ route('employeeattendancereportgenerate') }}",
                    method: "POST",
                    data: {
                        department: departmentID,
                        from_date: from_date,
                        to_date: to_date,
                        last_emp_id: lastEmpId,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (result) { 
                        $('#pdf_excel').html('<i class="fas fa-file-pdf mr-2"></i> Search').prop('disabled', false);                                        
                        if (result.length > 0) {
                            var html = '';

                            var obj = JSON.parse(result);
                            let datalist = obj[0].data;                            
                            
                            $.each(datalist, function (i, item) {                                
                                if (lastEmpId != datalist[i].id) {
                                    lastEmpId = datalist[i].id;
                                    let totot=0;
                                    let totdoubleot=0;
                                    let tottripleot=0;
                                    let totlatemin=0;

                                    html += '<table class="exporttable" style="border-collapse: collapse; font-size: 14px;" width="100%;"><tr><td colspan="12" style="padding-bottom: 10px;"><strong>Techspase (Pvt) Ltd</strong></td></tr><tr><td colspan="12" style="border-bottom: 1px solid black;padding-bottom: 10px;"><strong>Employee Monthly Attendance Details Report for the of </strong></td></tr><tr><td colspan="6" style="padding-top: 10px;"><strong>Emp No:</strong> '+datalist[i].emp_id+' </td><td colspan="6" style="padding-top: 10px;"><strong>Department:</strong> '+datalist[i].departmentname+' </td></tr></tr><tr><td colspan="6"><strong>Name:</strong> '+datalist[i].emp_fullname+' </td><td colspan="6"><strong>Gender:</strong> '+datalist[i].emp_gender+' </td></tr><tr><td colspan="6" style="border-bottom: 1px solid black;padding-bottom: 10px;"><strong>Designation:</strong> '+datalist[i].jobtitlename+' </td><td colspan="6" style="border-bottom: 1px solid black;padding-bottom: 10px;"><strong>Shift:</strong> '+datalist[i].shiftname+' </td></tr><tr><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">In Date</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Out Date</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Day Type</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Shift</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">In Time</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Out Time</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Late Min</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">OT Hr:Mi</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">DOT Hr:Mi</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">TOT Hr:Mi</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Leave Type</th><th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Leave Day</th></tr>';
                                    var objattendance = datalist[i].attendance;
                                    
                                    $.each(objattendance, function (j, item) {
                                        if(objattendance[j].in_time==null && objattendance[j].leave_type==''){
                                            html += '<tr><td>'+objattendance[j].in_date+'</td><td>&nbsp;</td><td>'+objattendance[j].day_type+'</td><td>'+objattendance[j].shift+'</td>&nbsp;<td></td>&nbsp;<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';   
                                        }
                                        else if(objattendance[j].in_time==null && objattendance[j].leave_type!=''){
                                            html += '<tr><td>'+objattendance[j].in_date+'</td><td>&nbsp;</td><td>'+objattendance[j].day_type+'</td><td>'+objattendance[j].shift+'</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>'+objattendance[j].leave_type+'</td><td>'+objattendance[j].leave_days+'</td></tr>';   
                                        }
                                        else{
                                            totot += objattendance[j].ot_hours;
                                            totdoubleot += objattendance[j].double_ot;
                                            tottripleot += objattendance[j].triple_ot;
                                            totlatemin += objattendance[j].late_min;

                                            html += '<tr><td>'+objattendance[j].in_date+'</td><td>'+objattendance[j].out_date+'</td><td>'+objattendance[j].day_type+'</td><td>'+objattendance[j].shift+'</td><td>'+objattendance[j].in_time+'</td><td>'+objattendance[j].out_time+'</td><td>'+objattendance[j].late_min+'</td><td>'+objattendance[j].ot_hours+'</td><td>'+objattendance[j].double_ot+'</td><td>'+objattendance[j].triple_ot+'</td><td>'+objattendance[j].leave_type+'</td><td>'+objattendance[j].leave_days+'</td></tr>';   
                                        }                                     
                                    });
                                    html += '<tr><td colspan="6">&nbsp</td><td style="border-top: 1px solid black;border-bottom: 2px double black;">'+parseFloat(totlatemin).toFixed(2)+'</td><td style="border-top: 1px solid black;border-bottom: 2px double black;">'+parseFloat(totot).toFixed(2)+'</td><td style="border-top: 1px solid black;border-bottom: 2px double black;">'+parseFloat(totdoubleot).toFixed(2)+'</td><td style="border-top: 1px solid black;border-bottom: 2px double black;">'+parseFloat(tottripleot).toFixed(2)+'</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                    html += '</table>';
                                }
                            });

                            lastEmpId = obj[0].lastEmpId;
                            $('#employee_list').append(html);

                            loading = false;

                            exportfunction();
                        } else {
                            $(window).off("scroll"); // Stop loading if no more data
                        }
                    }
                });
            }

            // Load first 20 employees on button click
            $('#pdf_excel').click(function () {
                $('#pdf_excel').html('<i class="fa fa-spinner fa-spin mr-2"></i> Searching').prop('disabled', true);
                $('#employee_list').empty(); // Clear existing list
                lastEmpId = 0;
                loadEmployees();
            });

            // Load more employees on scroll
            $(window).scroll(function () {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    loadEmployees();
                }
            });
        });

        function getMonthsBetween(startDate, endDate) {
            let months = [];
            let date = new Date(startDate);

            while (date <= endDate) {
                months.push(date.toLocaleString('default', { month: 'long', year: 'numeric' }));
                date.setMonth(date.getMonth() + 1);
            }

            return months;
        }

        function exportfunction(){
            $('#btnexport').click(function() {
                var { jsPDF } = window.jspdf;
                var doc = new jsPDF('p', 'pt', 'A4');
                
                // Get all the tables with the 'exporttable' class
                var tables = $('.exporttable');
                
                // Loop through each table and add it to the PDF
                tables.each(function(index, table) {
                    // Add the current table to the PDF
                    doc.autoTable({ 
                        html: table, 
                        startY: index === 0 ? 10 : 20, // Start position for first table and subsequent tables
                        margin: { top: 10, left: 10, right: 10 },
                        theme: 'striped', // Try 'grid' or 'striped' for better visual separation
                        styles: {
                            fontSize: 8,
                            cellPadding: 3, // Reduced padding to save space
                            overflow: 'linebreak', // Handle long content
                            valign: 'middle'
                        },
                        headStyles: {
                            fillColor: [200, 200, 200], // Light gray header
                            textColor: [0, 0, 0],
                            fontSize: 8
                        },
                        bodyStyles: {
                            textColor: [0, 0, 0]
                        },
                        // Add page break automatically when needed
                        didDrawPage: function(data) {
                            // Reset startY for next table to avoid gaps
                            if (data.pageCount > 1) {
                                doc.lastAutoTable.finalY = 20;
                            }
                        }
                    });
                    
                    // Add a page break only if there's more content to come
                    if (index < tables.length - 1) {
                        doc.addPage();
                    }
                });
                
                // Save the PDF
                var departmenttext = $("#department option:selected").text();;
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val(); 

                var doctitle = 'attendance_in_'+departmenttext+'_from_'+from_date+'_to_'+to_date;

                doc.save(doctitle+'.pdf');
            });
        }
    </script>

@endsection

