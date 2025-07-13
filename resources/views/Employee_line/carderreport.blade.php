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
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm" required>
                               
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Date</label>
                                <input type="date" class="form-control form-control-sm" name="reportdate" id="reportdate" required >
                            </div>
                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0 p-2">
                        <div class="response">
                            
                        </div>
                </div>
            </div>
        </div>
    </main>

@endsection


@section('script')
<script>
    $(document).ready(function() {

        $('#report_menu_link').addClass('active');
        $('#report_menu_link_icon').addClass('active');
        $('#employeedetailsreport').addClass('navbtnactive');

        let company = $('#company');
        let department = $('#department');

        $('#banks').select2({width: '100%'});
     
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

        function load_dt(department,reportdate){
            $('.response').html('');

                $.ajax({
                    url: "{{ route('generatecarderreport') }}",
                    method: "POST",
                    data: {
                         department :department,
                         reportdate :reportdate,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {
                        $('.response').html(res.html);
                    }
                });


        }

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department').val();
            let reportdate = $('#reportdate').val();
            load_dt(department,reportdate);
        });
    });



    async function generatePDF() {
        const {
            jsPDF
        } = window.jspdf;
        const {
            autoTable
        } = window.jspdf;

        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'mm'
        });

        const currentDate = new Date().toISOString().split('T')[0];
        let firstPage = true;

        // Calculate page dimensions
        const pageWidth = doc.internal.pageSize.getWidth();
        const maxUsableWidth = pageWidth - 10; // 5mm margin on each side

        // Column width configuration (total should be close to maxUsableWidth)
        const columnStyles = {
            0: {
                cellWidth: 32,
                halign: 'left'
            }, // Section column
            1: {
                cellWidth: 25
            }, // MO BUD
            2: {
                cellWidth: 16
            }, // MO ACTUAL
            3: {
                cellWidth: 20
            }, // MO PRESENT
            4: {
                cellWidth: 16
            }, // MO AB
            5: {
                cellWidth: 25
            }, // Helper BUD
            6: {
                cellWidth: 16
            }, // Helper ACTUAL
            7: {
                cellWidth: 20
            }, // Helper PRESENT
            8: {
                cellWidth: 16
            }, // Helper AB
            9: {
                cellWidth: 20
            }, // TOTAL BUD
            10: {
                cellWidth: 20
            }, // TOTAL CARDER
            11: {
                cellWidth: 20
            }, // AB
            12: {
                cellWidth: 20
            } // PRESENT
        };

        // Calculate total table width
        const tableWidth = Object.values(columnStyles).reduce((sum, style) => sum + style.cellWidth, 0);

        // Calculate horizontal centering offset
        const startX = (pageWidth - tableWidth) / 2;

        doc.autoTable({
            html: '#production_carderreport_table',
            theme: 'grid',
            startY: 40,
            margin: {
                top: 35
            },
            styles: {
                fontSize: 8,
                cellPadding: 2,
                lineWidth: 0.2,
                lineColor: [0, 0, 0],
                halign: 'center',
                valign: 'middle',
                overflow: 'linebreak'
            },
            headStyles: {
                fillColor: [220, 220, 220],
                textColor: [0, 0, 0],
                fontStyle: 'bold',
                halign: 'center',
                fontSize: 9
            },
            columnStyles: columnStyles,
            tableWidth: tableWidth,
            startX: startX, // Center the table horizontally
            didDrawPage: function (data) {
                if (firstPage) {
                    // Header with centered text
                    doc.setFontSize(18);
                    doc.setFont('Helvetica', 'bold');
                    doc.setTextColor(40);
                    centerText('PRODUCTION CARDER REPORT', 20, doc);

                    doc.setFontSize(14);
                    centerText('Techspace HRM', 28, doc);

                    doc.setFontSize(10);
                    doc.setFont('Helvetica', 'normal');
                    centerText('Date: ' + currentDate, 35, doc);

                    // Header line spanning the table width
                    doc.setDrawColor(200, 200, 200);
                    doc.line(startX, 38, startX + tableWidth, 38);
                } else {
                    doc.setFontSize(10);
                    doc.setFont('Helvetica', 'normal');
                    doc.text('Date: ' + currentDate, startX, 15);
                }
                firstPage = false;
            },
        });

        doc.save('Production_Carder_Report_' + currentDate + '.pdf');
    }

    async function generatePDFop2() {
        const {
            jsPDF
        } = window.jspdf;
        const {
            autoTable
        } = window.jspdf;

        const doc = new jsPDF({
            format: 'a4', // Changed to landscape for better fit
            unit: 'mm'
        });

        const currentDate = new Date().toISOString().split('T')[0];
        let firstPage = true;

        doc.autoTable({
            html: '#department2_carderreport_table',
            theme: 'grid',
            startY: 40,
            margin: {
                top: 35,
                left: 10, // Reduced margins for wider table
                right: 10
            },
            styles: {
                fontSize: 9,
                cellPadding: 3,
                lineWidth: 0.2,
                lineColor: [0, 0, 0],
                halign: 'center',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [220, 220, 220],
                textColor: [0, 0, 0],
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: {
                    cellWidth: 40,
                    halign: 'left'
                },
                1: {
                    cellWidth: 30
                }, // Day Actual
                2: {
                    cellWidth: 30
                }, // Night Actual
                3: {
                    cellWidth: 20
                }, // Day Present
                4: {
                    cellWidth: 20
                }, // Day AB
                5: {
                    cellWidth: 20
                }, // Night Present
                6: {
                    cellWidth: 20
                } // Night AB
            },
            didDrawPage: function (data) {
                const pageWidth = doc.internal.pageSize.getWidth();

                if (firstPage) {
                    // Page 1 Header
                    doc.setFontSize(18);
                    doc.setFont('Helvetica', 'bold');
                    doc.setTextColor(40);
                    centerText('Department Carder Report', 20, doc);

                    doc.setFontSize(14);
                    doc.setFont('Helvetica', 'bold');
                    centerText('Techspace HRM', 28, doc);

                    doc.setFontSize(10);
                    doc.setFont('Helvetica', 'normal');
                    centerText('Date: ' + currentDate, 35, doc);

                    doc.setDrawColor(200, 200, 200);
                    doc.line(10, 38, pageWidth - 10, 38);
                } else {
                    // Add space at top for subsequent pages
                    doc.setFontSize(10);
                    doc.setFont('Helvetica', 'normal');
                    doc.text('Date: ' + currentDate, 10, 15);
                }

                firstPage = false;
            }
        });

        doc.save('Department_Carder_Report_' + currentDate + '.pdf');
    }

   async function generatePDFop3() {
    try {
        const { jsPDF } = window.jspdf;
        const { autoTable } = window.jspdf;

        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        const currentDate = new Date().toISOString().split('T')[0];
        let startY = 40;

        // Verify tables exist before processing
        const mainTable = document.getElementById('department2_carderreport_table');
        const summaryTable1 = document.getElementById('summary_carderreport_table1');
        const summaryTable2 = document.getElementById('summary_carderreport_table2');

        if (!mainTable) {
            throw new Error('Main table not found');
        }

        // Main Department Carder Report Table
        doc.autoTable({
            html: '#department2_carderreport_table',
             theme: 'grid',
            startY: startY,
            margin: { top: 35, left: 15, right: 15 },
            styles: {
                fontSize: 9,
                cellPadding: 3,
                lineWidth: 0.2,
                lineColor: [0, 0, 0],
                halign: 'center',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [220, 220, 220],
                textColor: [0, 0, 0],
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: { cellWidth: 50, halign: 'left' },
                1: { cellWidth: 50 },
                2: { cellWidth: 30 },
                3: { cellWidth: 30 }
            },
            didDrawPage: function(data) {
                if (data.pageNumber === 1) {
                    doc.setFontSize(18);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Department Carder Report', 105, 20, { align: 'center' });
                    
                    doc.setFontSize(14);
                    doc.text('Techspace HRM', 105, 28, { align: 'center' });
                    
                    doc.setFontSize(10);
                    doc.text('Date: ' + currentDate, 105, 35, { align: 'center' });
                }
            }
        });

        // Update position for next table
        startY = doc.lastAutoTable.finalY + 15;

        // First Summary Table (if exists)
        if (summaryTable1) {
            doc.autoTable({
                html: '#summary_carderreport_table1',
                 theme: 'grid',
                startY: startY,
                margin: { left: 15, right: 15 },
                styles: {
                    fontSize: 9,
                    cellPadding: 3,
                     lineWidth: 0.2,
                    lineColor: [0, 0, 0],
                    halign: 'center'
                },
                headStyles: {
                    fillColor: [220, 220, 220],
                    textColor: [0, 0, 0],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                columnStyles: {
                    0: { cellWidth: 30, halign: 'left' },
                    1: { cellWidth: 40 },
                    2: { cellWidth: 40 },
                    3: { cellWidth: 25 },
                    4: { cellWidth: 25 }
                }
            });
            startY = doc.lastAutoTable.finalY + 15;
        }

        // Second Summary Table (if exists)
        if (summaryTable2) {
            doc.autoTable({
                html: '#summary_carderreport_table2',
                 theme: 'grid',
                startY: startY,
                margin: { left: 15, right: 15 },
                styles: {
                    fontSize: 9,
                    cellPadding: 3,
                     lineWidth: 0.2,
                     lineColor: [0, 0, 0],
                    halign: 'center',
                    valign: 'middle'
                },
                headStyles: {
                    fillColor: [220, 220, 220],
                    textColor: [0, 0, 0],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                columnStyles: {
                    0: { cellWidth: 30, halign: 'left' },
                    1: { cellWidth: 40 },
                    2: { cellWidth: 40 },
                    3: { cellWidth: 25 },
                    4: { cellWidth: 25 }
                }
            });
        }

        doc.save('Department_Carder_Report_' + currentDate + '.pdf');
    } catch (error) {
        console.error('PDF generation error:', error);
        alert('Error generating PDF: ' + error.message);
    }
}

   function centerText(text, y, doc) {
        const pageWidth = doc.internal.pageSize.getWidth();
        const textWidth = doc.getStringUnitWidth(text) * doc.internal.getFontSize() / doc.internal.scaleFactor;
        const x = (pageWidth - textWidth) / 2;
        doc.text(text, x, y);
    }


</script>
@endsection