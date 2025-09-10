@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

@include('layouts.navbars.auth.topnav', ['title' => 'Zones'])

<div class="container-fluid py-4">
    <div id="alert">
        @include('components.alert')
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="font-weight-bolder text-white mb-0"></h6>
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            + Add Employee
        </button>
    </div>

   <!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="addEmployeeForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="employee_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" id="employee_name" placeholder="Enter full name">
                            @error('name')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="employee_email" class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="employee_email" placeholder="Enter email">
                            @error('email')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                         <input type="text" name="fakeusernameremembered" style="display:none;">
                         <input type="password" name="fakepasswordremembered" style="display:none;">
                           <!-- Password -->
                        <div class="col-md-6">
                            <label for="employee_password" class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" id="employee_password" placeholder="Enter password">
                            @error('password')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <!-- Phone -->
                        <div class="col-md-6">
                            <label for="employee_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" placeholder="Enter phone number" autocomplete="off">
                            @error('phone')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                      

                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="employee_role" class="form-label">Role *</label>
                            <select class="form-select" name="role" id="employee_role">
                                <option value="">Select role</option>
                                <option value="ho">HO</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="employee">Employee</option>
                                <option value="complaint">Complaint</option>
                            </select>
                            <div class="text-danger small">
                                @error('role')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                         <!-- Supervisor -->
                       <div class="col-md-6">
                        <label for="employee_supervisor" class="form-label">Supervisor</label>
                        <select class="form-select" name="supervisor_id" id="employee_supervisor">
                            <option value="" selected hidden>Select Supervisor</option>
                            {{-- @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">
                                    {{ $supervisor->name }} ({{ $supervisor->email }})
                                </option>
                            @endforeach --}}
                        </select>
                        <div class="text-danger small">
                            @error('supervisor_id')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>


                        <!-- Zone -->
                        <div class="col-md-6">
                            <label for="employee_zone" class="form-label">Zone *</label>
                            <select class="form-select" name="zone_id" id="employee_zone">
                                <option value="" selected hidden>Select zone</option>
                                {{-- @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach --}}
                            </select>
                            <div class="text-danger small">
                                @error('zone_id')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6">
                            <label for="employee_location" class="form-label">Location</label>
                            <select class="form-select" name="location_id" id="employee_location">
                                <option value="" selected hidden>Select location</option>
                                {{-- @foreach($locations as $location)
                                    <option value="{{ $location->id }}" data-zone="{{ $location->zone_id }}">{{ $location->name }}</option>
                                @endforeach --}}
                            </select>
                            <div class="text-danger small">
                                @error('location_id')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                       
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add Employee</button>
                </div>
            </div>
        </form>
    </div>
</div>

   
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $(".form-select").chosen({
                placeholder_text_single:"What's your rating"
            });	
        });	
    </script>
@endsection

