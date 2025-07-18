<?php
?>
@extends('layouts.app')

@section('content')

<main style="padding-top: 2.5rem;">

    <div class="container-fluid mt-4 row invoice-card-row">

        <div class="col-3 h-300" >
            <div class="card bg-info invoice-card h-100">
                <div class="card-body d-flex justify-content-center align-items-center text-center">
                    <div>
                        <h1 class="text-dark fs-18" style="font-weight: bold; font-size: 30px;">Total Employees</h1>
                        <h2 class="text-dark invoice-num" style="font-size: 30px;"><a href="{{route('addEmployee')}}" class="no-underline">{{$empcount}}</a></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-xxl-9 col-sm-9">
            <div class="card h-300">
                <div class="card-body d-flex">
                    <table class="custom-table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>
                                <svg width="35px" height="34px">
                                <path fill-rule="evenodd"  d="M32.482,9.730 C31.092,6.789 28.892,4.319 26.120,2.586 C22.265,0.183 17.698,-0.580 13.271,0.442 C8.843,1.458 5.074,4.140 2.668,7.990 C0.255,11.840 -0.509,16.394 0.514,20.822 C1.538,25.244 4.224,29.008 8.072,31.411 C10.785,33.104 13.896,34.000 17.080,34.000 L17.286,34.000 C20.456,33.960 23.541,33.044 26.213,31.358 C26.991,30.866 27.217,29.844 26.725,29.067 C26.234,28.291 25.210,28.065 24.432,28.556 C22.285,29.917 19.799,30.654 17.246,30.687 C14.627,30.720 12.067,29.997 9.834,28.609 C6.730,26.671 4.569,23.644 3.752,20.085 C2.934,16.527 3.546,12.863 5.486,9.763 C9.488,3.370 17.957,1.418 24.359,5.414 C26.592,6.808 28.360,8.793 29.477,11.157 C30.568,13.460 30.993,16.016 30.707,18.539 C30.607,19.448 31.259,20.271 32.177,20.371 C33.087,20.470 33.911,19.820 34.011,18.904 C34.363,15.764 33.832,12.591 32.482,9.730 L32.482,9.730 Z"/>
                                <path fill-rule="evenodd"  d="M22.593,11.237 L14.575,19.244 L11.604,16.277 C10.952,15.626 9.902,15.626 9.250,16.277 C8.599,16.927 8.599,17.976 9.250,18.627 L13.399,22.770 C13.725,23.095 14.150,23.254 14.575,23.254 C15.001,23.254 15.427,23.095 15.753,22.770 L24.940,13.588 C25.592,12.937 25.592,11.888 24.940,11.237 C24.289,10.593 23.238,10.593 22.593,11.237 L22.593,11.237 Z"/>
                                </svg> &nbsp; Attendance</th>
                            <th>
                                <svg  width="35px" height="34px">
                                    <path fill-rule="evenodd"   d="M33.002,9.728 C31.612,6.787 29.411,4.316 26.638,2.583 C22.781,0.179 18.219,-0.584 13.784,0.438 C9.356,1.454 5.585,4.137 3.178,7.989 C0.764,11.840 -0.000,16.396 1.023,20.825 C2.048,25.247 4.734,29.013 8.584,31.417 C11.297,33.110 14.409,34.006 17.594,34.006 L17.800,34.006 C20.973,33.967 24.058,33.050 26.731,31.363 C27.509,30.872 27.735,29.849 27.243,29.072 C26.751,28.296 25.727,28.070 24.949,28.561 C22.801,29.922 20.314,30.660 17.761,30.693 C15.141,30.726 12.581,30.002 10.346,28.614 C7.241,26.675 5.080,23.647 4.262,20.088 C3.444,16.515 4.056,12.850 5.997,9.748 C10.001,3.353 18.473,1.401 24.876,5.399 C27.110,6.793 28.879,8.779 29.996,11.143 C31.087,13.447 31.513,16.004 31.227,18.527 C31.126,19.437 31.778,20.260 32.696,20.360 C33.607,20.459 34.432,19.809 34.531,18.892 C34.884,15.765 34.352,12.591 33.002,9.728 L33.002,9.728 Z"/>
                                    <path fill-rule="evenodd" d="M23.380,11.236 C22.728,10.585 21.678,10.585 21.026,11.236 L17.608,14.656 L14.190,11.243 C13.539,10.592 12.488,10.592 11.836,11.243 C11.184,11.893 11.184,12.942 11.836,13.593 L15.254,17.006 L11.836,20.420 C11.184,21.071 11.184,22.120 11.836,22.770 C12.162,23.096 12.588,23.255 13.014,23.255 C13.438,23.255 13.864,23.096 14.190,22.770 L17.608,19.357 L21.026,22.770 C21.352,23.096 21.777,23.255 22.203,23.255 C22.629,23.255 23.054,23.096 23.380,22.770 C24.031,22.120 24.031,21.071 23.380,20.420 L19.962,17.000 L23.380,13.587 C24.031,12.936 24.031,11.887 23.380,11.236 L23.380,11.236 Z"/>
                                    </svg>&nbsp; Late Attendance</th>
                            <th>
                                <svg  width="33px" height="32px">
                                    <path fill-rule="evenodd" 
                                     d="M31.963,30.931 C31.818,31.160 31.609,31.342 31.363,31.455 C31.175,31.538 30.972,31.582 30.767,31.583 C30.429,31.583 30.102,31.463 29.845,31.243 L25.802,27.786 L21.758,31.243 C21.502,31.463 21.175,31.583 20.837,31.583 C20.498,31.583 20.172,31.463 19.915,31.243 L15.872,27.786 L11.829,31.243 C11.622,31.420 11.370,31.534 11.101,31.572 C10.832,31.609 10.558,31.569 10.311,31.455 C10.065,31.342 9.857,31.160 9.710,30.931 C9.565,30.703 9.488,30.437 9.488,30.167 L9.488,17.416 L2.395,17.416 C2.019,17.416 1.658,17.267 1.392,17.001 C1.126,16.736 0.976,16.375 0.976,16.000 L0.976,6.083 C0.976,4.580 1.574,3.139 2.639,2.076 C3.703,1.014 5.146,0.417 6.651,0.417 L26.511,0.417 C28.016,0.417 29.459,1.014 30.524,2.076 C31.588,3.139 32.186,4.580 32.186,6.083 L32.186,30.167 C32.186,30.437 32.109,30.703 31.963,30.931 ZM9.488,6.083 C9.488,5.332 9.189,4.611 8.657,4.080 C8.125,3.548 7.403,3.250 6.651,3.250 C5.898,3.250 5.177,3.548 4.645,4.080 C4.113,4.611 3.814,5.332 3.814,6.083 L3.814,14.583 L9.488,14.583 L9.488,6.083 ZM29.348,6.083 C29.348,5.332 29.050,4.611 28.517,4.080 C27.985,3.548 27.263,3.250 26.511,3.250 L11.559,3.250 C12.059,4.111 12.324,5.088 12.325,6.083 L12.325,27.092 L14.950,24.840 C15.207,24.620 15.534,24.500 15.872,24.500 C16.210,24.500 16.537,24.620 16.794,24.840 L20.837,28.296 L24.880,24.840 C25.137,24.620 25.463,24.500 25.802,24.500 C26.140,24.500 26.467,24.620 26.724,24.840 L29.348,27.092 L29.348,6.083 ZM25.092,20.250 L16.581,20.250 C16.205,20.250 15.844,20.101 15.578,19.835 C15.312,19.569 15.162,19.209 15.162,18.833 C15.162,18.457 15.312,18.097 15.578,17.831 C15.844,17.566 16.205,17.416 16.581,17.416 L25.092,17.416 C25.469,17.416 25.829,17.566 26.096,17.831 C26.362,18.097 26.511,18.457 26.511,18.833 C26.511,19.209 26.362,19.569 26.096,19.835 C25.829,20.101 25.469,20.250 25.092,20.250 ZM25.092,14.583 L16.581,14.583 C16.205,14.583 15.844,14.434 15.578,14.168 C15.312,13.903 15.162,13.542 15.162,13.167 C15.162,12.791 15.312,12.430 15.578,12.165 C15.844,11.899 16.205,11.750 16.581,11.750 L25.092,11.750 C25.469,11.750 25.829,11.899 26.096,12.165 C26.362,12.430 26.511,12.791 26.511,13.167 C26.511,13.542 26.362,13.903 26.096,14.168 C25.829,14.434 25.469,14.583 25.092,14.583 ZM25.092,8.916 L16.581,8.916 C16.205,8.916 15.844,8.767 15.578,8.501 C15.312,8.236 15.162,7.875 15.162,7.500 C15.162,7.124 15.312,6.764 15.578,6.498 C15.844,6.232 16.205,6.083 16.581,6.083 L25.092,6.083 C25.469,6.083 25.829,6.232 26.096,6.498 C26.362,6.764 26.511,7.124 26.511,7.500 C26.511,7.875 26.362,8.236 26.096,8.501 C25.829,8.767 25.469,8.916 25.092,8.916 Z"/>
                                    </svg>&nbsp; Absent</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="row-label">Today</td>
                            <td><h2 class="text-dark" style="font-size:30px; text-align:center;">  
                                <a href="#" id="attendancebtn" class="no-underline"> {{$todaycount}} </a></h2>
                            </td>
                            <td><h2 class="text-dark" style="font-size:30px; text-align:center;">  
                                <a href="#" id="lateattendancebtn" class="no-underline"> {{$todaylatecount}} </a></h2>
                            </td>
                            <td><h2 class="text-dark" style="font-size:30px; text-align:center;">  
                                <a href="#" id="absentbtn" class="no-underline"> {{$empcount-$todaycount}} </a></h2>
                            </td>
                        </tr>
                        <tr>
                            <td class="row-label">Yesterday</td>
                            <td><h2 class="text-dark" style="font-size:30px; text-align:center;">  
                                <a href="#" id="yesterdayattendancebtn" class="no-underline"> {{$yesterdaycount}} </a></h2>
                            </td>
                            <td><h2 class="text-dark" style="font-size:30px; text-align:center;">  
                                <a href="#" id="yesterdaylateattendancebtn" class="no-underline"> {{$yesterdaylatecount}} </a></h2>
                            </td>
                            <td><h2 class="text-dark" style="font-size:30px; text-align:center;">  
                                <a href="#" id="yesterdayabsentbtn" class="no-underline"> {{$empcount-$yesterdaycount}}</a></h2>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid mt-4 row invoice-card-row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap border-0 pb-0">
                    <div class="me-auto mb-sm-0 mb-3">
                        <h4 class="card-title mb-2">Attendant of the Employees</h4>
                    </div>  
                </div>
                <div class="card-body pb-2">
                    <canvas id="myAreaChart" width="100%" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4 row invoice-card-row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap border-0 pb-0">
                    <div class="me-auto mb-sm-0 mb-3">
                        <h4 class="card-title mb-2">Attendant of the Employees Line Chart</h4>
                    </div>  
                </div>
                <div class="card-body pb-2">
                    <canvas id="myLineChart" width="100%" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>

</main>

<div class="modal fade" id="attendanceformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Department Wise Attendance (<?php echo date('Y-m-d') ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <div id="attandancetable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="lateattendanceformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Late Attendance (<?php echo date('Y-m-d') ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <div id="lateattandancetable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="absentformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Department Wise Absent (<?php echo date('Y-m-d') ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <div id="absenttable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="yesterdayattendanceformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Yesterday Attendance (<?php echo date('Y-m-d', strtotime('-1 day')); ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <div id="yesterdayattandancetable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="yesterdaylateattendanceformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Yesterday Late Attendance (<?php echo date('Y-m-d', strtotime('-1 day')); ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <div id="yesterdaylateattandancetable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="yesterdayabsentformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Yesterday Absent (<?php echo date('Y-m-d', strtotime('-1 day')); ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 40rem; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <div id="yesterdayabsenttable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
      

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

<script>
$(document).ready( function () {

    $('#dashboard_link').addClass('active');
    $('#dashboard_link_icon').addClass('active');

    getattend();
    getattend_linechart();
   
    // today part
    $('#attendancebtn').click(function(){
        $.ajax({
            url: "{{ route('getdashboard_department_attendance') }}",
            method: "GET",
            // data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

               $('#attandancetable').html(data.result)
            }
        });

        $('#attendanceformModal').modal('show');
    });

    $('#lateattendancebtn').click(function(){
        $.ajax({
            url: "{{ route('getdashboard_department_lateattendance') }}",
            method: "GET",
            // data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

               $('#lateattandancetable').html(data.result)
            }
        });

        $('#lateattendanceformModal').modal('show');
    });

    $('#absentbtn').click(function(){
        $.ajax({
            url: "{{ route('getdashboard_department_absent') }}",
            method: "GET",
            // data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

               $('#absenttable').html(data.result)
            }
        });

        $('#absentformModal').modal('show');
    });

    // yesterday part
    $('#yesterdayattendancebtn').click(function(){
        $.ajax({
            url: "{{ route('getdashboard_department_yesterdayattendance') }}",
            method: "GET",
            // data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

               $('#yesterdayattandancetable').html(data.result)
            }
        });

        $('#yesterdayattendanceformModal').modal('show');
    });

    $('#yesterdaylateattendancebtn').click(function(){
        $.ajax({
            url: "{{ route('getdashboard_department_yesterdaylateattendance') }}",
            method: "GET",
            // data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

               $('#yesterdaylateattandancetable').html(data.result)
            }
        });

        $('#yesterdaylateattendanceformModal').modal('show');
    });

    $('#yesterdayabsentbtn').click(function(){
        $.ajax({
            url: "{{ route('getdashboard_department_yesterdayabsent') }}",
            method: "GET",
            // data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

               $('#yesterdayabsenttable').html(data.result)
            }
        });

        $('#yesterdayabsentformModal').modal('show');
    });

    showTime();

    function showTime(){
        var date = new Date();
        var h = date.getHours(); // 0 - 23
        var m = date.getMinutes(); // 0 - 59
        var s = date.getSeconds(); // 0 - 59
        var session = "AM";

        if(h == 0){
            h = 12;
        }

        if(h > 12){
            h = h - 12;
            session = "PM";
        }

        h = (h < 10) ? "0" + h : h;
        m = (m < 10) ? "0" + m : m;
        s = (s < 10) ? "0" + s : s;

        var time = h + ":" + m + ":" + s + " " + session;
        document.getElementById("clock").innerText = time;
        document.getElementById("clock").textContent = time;

        setTimeout(showTime, 1000);
    }


    // getbranchattend();
  
} );
function getattend(){
    var empcount={{$empcount}}

        var url = "{{url('getdashboard_AttendentChart')}}";
        var date = new Array();
        var Labels = new Array();
        var count = new Array();
        var absent_count = new Array();
        $(document).ready(function(){
          $.get(url, function(response){
            response.forEach(function(data){
                const editedText = data.date.slice(0, -8)
                date.push(editedText);               
                count.push(data.count);
                absent_count.push(empcount-(data.count));
            });
            var ctx = document.getElementById("myAreaChart");
                var myChart = new Chart(ctx, {
                  type: 'bar',
                  data: {
                      labels:date,
                      datasets: [{
                          label: 'Attendent',
                          data: count,
                          backgroundColor: 'rgb(75, 192, 192)',
                          borderWidth: 1
                      }, {
                    label: 'Absences',
                    data: absent_count,
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }]
                  },
                  options: {
                      scales: {
                          yAxes: [{
                              ticks: {
                                  beginAtZero:true
                              }
                          }]
                      },
                      tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: "#6e707e",
            titleFontSize: 14,
            borderColor: "#dddfeb",
           
        }
                      
                  }
              });
          });
        });
};

// line chart
function getattend_linechart(){
    var empcount={{$empcount}}
    var url = "{{url('getdashboard_AttendentChart')}}";
    var date = new Array();
    var Labels = new Array();
    var attendance_count = new Array();
    var absent_count = new Array();
    $(document).ready(function(){
      $.get(url, function(response){
        response.forEach(function(data){
            const editedText = data.date.slice(0, -8)
            date.push(editedText);               
            attendance_count.push(data.count);
            absent_count.push(empcount-(data.count));
        });
        var ctx = document.getElementById('myLineChart').getContext('2d');
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels:date,
                datasets: [{
                    label: 'Attendance',
                    borderColor: 'rgb(75, 192, 192)',
                    data: attendance_count,
                    fill: false
                }, {
                    label: 'Absences',
                    borderColor: 'rgb(255, 99, 132)',
                    data: absent_count,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Set to true to maintain aspect ratio
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
      });
    });
};
   
        </script>

<script>
$(document).ready( function () {
    $('#empTable').DataTable();
} );
</script>

@endsection