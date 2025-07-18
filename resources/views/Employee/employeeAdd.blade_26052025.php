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
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Location</label>
                            <select name="location" id="location_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Employee</label>
                            <select name="employee" id="employee_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-dark">Date : From - To</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">

                                <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
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
                <div class="row">
                    <div class="col-12">
                        @can('employee-create')
                            <button type="button" class="btn btn-outline-success btn-sm fa-pull-right ml-2" name="upload_record" id="upload_record"><i class="fas fa-upload mr-2"></i>Upload Employee</button>
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Employee</button>
                        @endcan
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                            <thead>
                                <tr>
                                    <th>Emp ID</th>
                                    <th>Name</th>
                                    <th>Office</th>
                                    <th>Department</th>
                                    <th>Start date</th>
                                    <th>Position</th>
                                    <th>Basic Salary</th>
                                    <th>BR 01</th>
                                    <th>BR 02</th>
                                    <th>Total Salary</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Actions</th>
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
    <div class="modal fade" id="empModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Employee Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formemployee" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">EPF No*</label>
                                        <input type="text" name="etfno" id="etfno" class="form-control form-control-sm" required />
                                        @if ($errors->has('etfno'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('etfno') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Employee ID*</label>
                                        <input type="text" name="emp_id" id="emp_id" class="form-control form-control-sm {{ $errors->has('emp_id') ? ' has-error' : '' }}" required />
                                         @if ($errors->has('emp_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('emp_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">First Name*</label>
                                        <input type="text" name="firstname" id="firstname" class="form-control form-control-sm {{ $errors->has('firstname') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('firstname'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('firstname') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Middle Name</label>
                                        <input type="text" name="middlename" id="middlename" class="form-control form-control-sm {{ $errors->has('middlename') ? ' has-error' : '' }}" />
                                        @if ($errors->has('middlename'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('middlename') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Last Name</label>
                                        <input type="text" name="lastname" id="lastname" class="form-control form-control-sm {{ $errors->has('lastname') ? ' has-error' : '' }}" />
                                        @if ($errors->has('lastname'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lastname') }}</strong>
                                        </span>
                                        @endif
                                    </div>                                    
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Full Name</label>
                                        <input type="text" name="emp_fullname" id="emp_fullname" class="form-control form-control-sm  {{ $errors->has('emp_fullname') ? ' has-error' : '' }}" />
                                        @if ($errors->has('emp_fullname'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('emp_fullname') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Name with Initial*</label>
                                        <input type="text" name="emp_name_with_initial" id="emp_name_with_initial" class="form-control form-control-sm  {{ $errors->has('emp_name_with_initial') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('emp_name_with_initial'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('emp_name_with_initial') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Calling Name*</label>
                                        <input type="text" name="calling_name" id="calling_name" class="form-control form-control-sm {{ $errors->has('calling_name') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('calling_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('calling_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Identity Card No*</label>
                                        <input type="text" name="emp_id_card" id="emp_id_card" class="form-control form-control-sm {{ $errors->has('emp_id_card') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('emp_id_card'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('emp_id_card') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Telephone</label>
                                        <input type="text" name="telephone" id="telephone" class="form-control form-control-sm {{ $errors->has('telephone') ? ' has-error' : '' }}" />
                                        @if ($errors->has('telephone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('telephone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Mobile No*</label>
                                        <input type="text" name="emp_mobile" id="emp_mobile" class="form-control form-control-sm {{ $errors->has('emp_mobile') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('emp_mobile'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('emp_mobile') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Office Telephone</label>
                                        <input type="text" name="emp_work_telephone" id="emp_work_telephone" class="form-control form-control-sm {{ $errors->has('emp_work_telephone') ? ' has-error' : '' }}" />
                                        @if ($errors->has('emp_work_telephone'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('emp_work_telephone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Photograph</label>
                                        <input type="file" data-preview="#preview" class="form-control form-control-sm {{ $errors->has('photograph') ? ' has-error' : '' }}" name="photograph" id="photograph">
                                        <img class="" id="preview" src="" style="max-width: 200px; max-height: 200px; width: auto; height: auto;">
                                        @if ($errors->has('photograph'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('photograph') }}</strong>
                                        </span>
                                        @endif
                                    </div>                                   
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Employee Status*</label>
                                        <select name="status" id="status" class="form-control form-control-sm shipClass {{ $errors->has('status') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($employmentstatus as $employmentstatu)
                                            <option value="{{$employmentstatu->id}}">{{$employmentstatu->emp_status}}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('status'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('status') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Work Location*</label>
                                        <select name="location" class="form-control form-control-sm shipClass {{ $errors->has('location') ? ' has-error' : '' }}" required>
                                            <option value="">Please Select</option>
                                            @foreach($branch as $branches)
                                            <option value="{{$branches->id}}">{{$branches->location}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('location'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('location') }}</strong>
                                        </span>
                                        @endif
                                    </div>                                    
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Employee Job*</label>
                                        <select name="employeejob" class="form-control form-control-sm shipClass {{ $errors->has('employeejob') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($title as $titles)
                                            <option value="{{$titles->id}}">{{$titles->title}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('employeejob'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('employeejob') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Work Shift*</label>
                                        <select name="shift" class="form-control form-control-sm shipClass {{ $errors->has('shift') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($shift_type as $shift_types)
                                            <option value="{{$shift_types->id}}">{{$shift_types->shift_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('shift'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('shift') }}</strong>
                                        </span>
                                        @endif
                                    </div>                                    
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Company*</label>
                                        <select name="employeecompany" id="company" class="form-control form-control-sm shipClass {{ $errors->has('employeecompany') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($company as $companies)
                                            <option value="{{$companies->id}}">{{$companies->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('employeecompany'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('employeejob') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Department*</label>
                                        <select name="department" id="department" class="form-control form-control-sm shipClass {{ $errors->has('department') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($departments as $dept)
                                                <option value="{{$dept->id}}">{{$dept->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('department'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('department') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row mb-1 no_of_leaves" hidden>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">No of Casual Leaves</label>
                                        <input type="number" name="no_of_casual_leaves" id="no_of_casual_leaves" class="form-control form-control-sm {{ $errors->has('no_of_casual_leaves') ? ' has-error' : '' }}" />
                                        @if ($errors->has('no_of_casual_leaves'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('no_of_casual_leaves') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">No of Annual Leaves</label>
                                        <input type="number" name="no_of_annual_leaves" id="no_of_annual_leaves" class="form-control form-control-sm {{ $errors->has('no_of_annual_leaves') ? ' has-error' : '' }}" />
                                        @if ($errors->has('no_of_annual_leaves'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('no_of_annual_leaves') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <button type="reset" class="btn btn-outline-primary btn-sm"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>   
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="userlogModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee User Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="userlogform_result"></span>

                            <form id="userlogform" method="post">
                                {{ csrf_field() }}
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">E-mail</label>
                                    <input type="text" class="form-control form-control-sm {{ $errors->has('email') ? ' has-error' : '' }} shipClass" id="email" name="email" placeholder="Type Employee Email">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Password</label>
                                    <input type="password" class="form-control form-control-sm {{ $errors->has('password') ? ' has-error' : '' }} shipClass" id="inputEmail4" name="password">
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Confirm Password</label>
                                    <input type="password" class="form-control form-control-sm {{ $errors->has('comfirmpassword') ? ' has-error' : '' }} shipClass" id="password-confirm" name="password_confirmation">
                                    @if ($errors->has('comfirmpassword'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('comfirmpassword') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" id="userid" name="userid">
                                <input type="hidden" id="name" name="name">
                                <input type="hidden" name="action" id="action" />
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
    <div class="modal fade" id="fpModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee User Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div id="fpform_result"></div>
                            <form id="fpform" method="post">
                                {{ csrf_field() }}
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">ID</label>
                                    <input type="text" name="id" id="id" class="form-control form-control-sm" readonly />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Emp Id: </label>
                                    <input type="text" name="userid" id="userid" class="form-control form-control-sm" readonly />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">name: </label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">cardno: </label>
                                    <input type="text" name="cardno" id="cardno" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">role: </label>
                                    <select name="role" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        <option value="0">User</option>
                                        <option value="4">Admin</option>
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">password: </label>
                                    <input type="text" name="password" id="password" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">FP Location: </label>
                                    <select name="devices" class="form-control form-control-sm shipClass">
                                        <option value="">Select</option>
                                        @foreach($device as $devices)
                                        <option value="{{$devices->ip}}">{{$devices->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->
</main>
    
{{-- resignation model --}}
<div class="modal fade" id="resignationformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Resignation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col"> 
                        <div id="resignform_result"></div>
                    <div class="form-group mb-1">
                        <label class="small font-weight-bold text-dark">Resign Date</label>
						<input type="date" class="form-control form-control-sm" id="resigndate" name="resigndate">
                    </div>

					<div class="form-group mb-1">
						<label class="small font-weight-bold text-dark">Comment</label>
						<textarea class="form-control form-control-sm" id="resignremark" name="resignremark"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <button type="button" name="resign_button" id="resign_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4 resign_button"><i class="fas fa-plus"></i>&nbsp;approve</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
      
<div class="modal fade" id="confirmModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
                        <h4 class="font-weight-normal">Are you sure you want to Confirm this data?</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" name="ok_button2" id="ok_button2" class="btn btn-danger px-3 btn-sm">OK</button>
                <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- CSV Modal -->
<div class="modal fade" id="uploadModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Upload Employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result1"></span>
                            <form method="post" id="formTitle1" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-group mb-1">
                                    <label class="control-label col" >
                                    File Content :
                                        <a class="col" href="{{ url('/public/csvsample/add_employees_format.csv') }}">
                                        CSV Format-Download Sample File
                                        </a>
                                    </label>
                                </div>	
                                <div class="fields">
                                    <div class="input-group mb-3">
                                        <input type="file" class="form-control" id="import_csv" name="import_csv" accept=".csv" required>
                                        <label class="input-group-text" for="import_csv">Upload</label>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-success btn-sm">
                                        <i class="fas fa-upload mr-2"></i>Import CSV
                                    </button>                               
                                 </div>
                                <input type="hidden" name="action" id="action" value="Upload" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
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
    $('#employeeinformation').addClass('navbtnactive');

    $("#etfno").focusout(function(){
        let val = $(this).val();
        $('#emp_id').val(val);
    });

    let company_f = $('#company_f');
    let department_f = $('#department_f');
    let employee_f = $('#employee_f');
    let location_f = $('#location_f');

    company_f.select2({
        placeholder: 'Select a Company',
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
        placeholder: 'Select a Department',
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
        placeholder: 'Select a Employee',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || ''
                }
            },
            cache: true
        }
    });

    location_f.select2({
        placeholder: 'Select Location',
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

    function load_dt(department, employee, location, from_date, to_date){
        $('#emptable').DataTable({
            lengthMenu: [[10, 25, 50, 100, 500, 700, -1], [10, 25, 50, 100, 500, 700, "All"]],
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
                "url": "{!! route('employee_list_dt') !!}",
                "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
            },
            columns: [
                { data: 'emp_id_link', name: 'emp_id_link' },
                { data: 'emp_name_link', name: 'emp_name_with_initial' },
                { data: 'location', name: 'branches.location' },
                { data: 'dep_name', name: 'departments.name' },
                { data: 'emp_join_date', name: 'emp_join_date' },
                { data: 'title', name: 'job_titles.title' },
                { 
                    data: 'basic_salary', 
                    name: 'basic_salary',
                    render: function(data, type) {
                        if (type === 'display') {
                            return 'Rs. ' + parseFloat(data).toFixed(2);
                        }
                        return data;
                    }
                },
                { 
                    data: 'br1', 
                    name: 'br1',
                    render: function(data, type) {
                        if (type === 'display') {
                            return 'Rs. ' + parseFloat(data || 0).toFixed(2);
                        }
                        return data;
                    }
                },
                { 
                    data: 'br2', 
                    name: 'br2',
                    render: function(data, type) {
                        if (type === 'display') {
                            return 'Rs. ' + parseFloat(data || 0).toFixed(2);
                        }
                        return data;
                    }
                },
                { 
                    data: 'total', 
                    name: 'total',
                    render: function(data, type) {
                        if (type === 'display') {
                            return 'Rs. ' + parseFloat(data || 0).toFixed(2);
                        }
                        return data;
                    }
                },
                { data: 'emp_status_label', name: 'emp_status_label' },
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "bDestroy": true,
            "order": [
                [5, "desc"]
            ]
        });
    }

    load_dt('', '', '', '', '');

    $('#from_date').on('change', function() {
        let fromDate = $(this).val();
        $('#to_date').attr('min', fromDate); 
    });

    $('#to_date').on('change', function() {
        let toDate = $(this).val();
        $('#from_date').attr('max', toDate); 
    });

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department_f').val();
        let employee = $('#employee_f').val();
        let location = $('#location_f').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
    });

    document.getElementById('btn-clear').addEventListener('click', function() {
    document.getElementById('formFilter').reset();

        $('#company_f').val('').trigger('change');   
        $('#location_f').val('').trigger('change');
        $('#department_f').val('').trigger('change');
        $('#employee_f').val('').trigger('change');
        $('#from_date').val('');                     
        $('#to_date').val('');                       

        // load_dt('', '', '', '', '');
    });


});
$(document).ready(function () {

    let company = $('#company');
    let department = $('#department');
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

    $('#create_record').click(function () {

        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#empModal').modal('show');
        $('.modal-title').text('Add Employee Record');
    });

    $('#formemployee').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';
        var formData = new FormData(this);
        //alert(formData);

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('empoyeeRegister') }}";
        }

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: action_url,
            method: "POST",
            //data:$(this).serialize(),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (data) {

                var html = '';
                if (data.errors) {
                    var errorMessages = '';
                    for (var count = 0; count < data.errors.length; count++) {
                        errorMessages += data.errors[count] + '\n';
                    }
                    alert("Error(s):\n" + errorMessages); 
                }
                if (data.success) {
                    alert("Success: " + data.success);
                    $('#formemployee')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                    $('#formemployee').modal('hide');
                }
                $('#form_result').html(html);
            }
        });
    });

    $('#upload_record').click(function() {
        $('.modal-title').text('Upload Employees Record');
        $('#action_button').html('Import');
        $('#action').val('Upload');
        $('#form_result1').html('');
        $('#formTitle1')[0].reset();

        $('#uploadModal').modal('show'); 
    });

    $('#formTitle1').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this); 
        var fileInput = $('#import_csv')[0].files[0];

        if (!fileInput || fileInput.type !== 'text/csv') {
            alert('Please upload a valid CSV file.');
            return;
        }

        $.ajax({
            url: "{{ route('import') }}",
            method: "POST",
            data: formData,
            processData: false, 
            contentType: false, 
            dataType: "json",
            success: function(data) {
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
                    $('#formTitle1')[0].reset();
                    location.reload();
                }
                $('#form_result1').html(html);
            },
            error: function(xhr, status, error) {
                $('#form_result1').html('<div class="alert alert-danger">An unexpected error occurred. Please try again.</div>');
            }
        });
    });



    /*

 $(document).on('click', '.edit', function(){
  var id = $(this).attr('id');
  $('#form_result').html('');
  $.ajax({
   url :"/EmploymentStatus/"+id+"/edit",
   dataType:"json",
   success:function(data)
   {
    $('#ip').val(data.result.ip);
    $('#name').val(data.result.name);
    $('#location').val(data.result.location);
    $('#hidden_id').val(id);
    $('.modal-title').text('Edit Fingerprint Device');
    $('#action_button').val('Edit');
    $('#action').val('Edit');
    $('#empModal').modal('show');
   }
  })
 });
*/
    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "EmployeeDestroy/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
                location.reload();
            }
        })
    });


    //userlog 

    $(document).on('click', '.adduserlog', function () {
        $('.modal-title').text('Add Employee User Login');
        $('#action_button').val('Add');
        $('#userlogform #id').val($(this).attr('data-id'));
        var id = $(this).attr('id');
        var name = $(this).attr('name');
        $('#userid').val(id);
        $('#name').val(name);
        $('#action').val('Add');
        $('#userlogform_result').html('');

        $('#userlogModal').modal('show');
    });

    $('#userlogform').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addUserLogin') }}";
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
                    // $('#fpform')[0].close();
                    //$('#emptable').DataTable().ajax.reload();
                    location.reload();
                }
                $('#userlogform_result').html(html);
            }
        });
    });

    //userlog

    //fingerprint 

    $(document).on('click', '.addfp', function () {
        $('.modal-title').text('Add Employee to Fingerprint');
        emp_id = $(this).attr('id');
        name = $(this).attr('name');

        $('#action_button').val('Add');
        $('#fpform #id').val(emp_id);
        $('#fpform  #userid').val(emp_id);
        $('#fpform  #name').val(name);

        $('#action').val('Add');
        $('#fpform_result').html('');

        $('#fpModal').modal('show');
    });

    $('#fpform').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addFingerprintUser') }}";
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
                    $('#fpform_result').html(html);
                    $('#fpform')[0].close();
                    //$('#emptable').DataTable().ajax.reload();

                    location.reload();
                }

            }
        });
    });

    //fingerprint

    // resination
    $(document).on('click', '.resign', function () {
        user_id = $(this).attr('id');

        $('#resignform_result').html('');
        $('#resignationformModal').modal('show');
        $('#resigndate').val(''); 

        $.ajax({
            url: '/getEmployeeJoinDate', 
            type: 'GET',
            data: { id: user_id },
            success: function (response) {
                if (response.join_date) {
                    $('#resigndate').attr('min', response.join_date);
                } else {
                    alert('Failed to fetch the join date.');
                }
            },
            error: function () {
                alert('An error occurred while fetching the join date.');
            }
        });
    });
    
    $(document).on('click', '.resign_button', function () {
        var checkresignationdate=$('#resigndate').val()
        if(checkresignationdate==''){
        alert('Please Select Date');
        }
        else{
            $('#confirmModal2').modal('show');
        }
       
    });

    $('#ok_button2').click(function () {
        var resignationdate= $('#resigndate').val();
        var resignationremark= $('#resignremark').val();
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
        $.ajax({
            url: '{!! route("employeeresignation") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    recordID: user_id,
                    resignationdate: resignationdate,
                    resignationremark: resignationremark,
                },
            beforeSend: function () {
                $('#ok_button2').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
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
                    $('#resignform_result').html(html);

                    $('#confirmModal2').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                }, 2000);
                location.reload();
            }
        })
    });



});


</script>

@endsection