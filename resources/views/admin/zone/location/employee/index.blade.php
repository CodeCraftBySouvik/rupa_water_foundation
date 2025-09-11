@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

@include('layouts.navbars.auth.topnav', ['title' => 'Zones'])

<div class="container-fluid py-4">
    <div id="alert">
        @include('components.alert')
    </div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="font-weight-bolder mb-0">Employee Directory</h6>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addEmployeeModal">
                + Add Employee
            </button>
        </div>

        <div class="card-body">
            {{-- <div class="table-responsive"> --}}
                <table class="table align-items-center">
                    <thead>
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sl.No</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zone
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Location
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Supervisor
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody id="employeeList">
                        {{-- Dynamic employee rows will be prepended here --}}
                        @forelse($userlist as $key=> $data)
                        <tr class="text-center">
                            <td>
                                <h6 class="mb-0 text-sm">{{$key + 1}}</h6>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ucwords($data->name)}}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ucwords($data->role)}}</p>
                            </td>
                            {{-- @dd($data->zones) --}}
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    {{ucwords($data->zones->pluck('name')->implode(', '))}}</p>
                            </td>

                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    {{ucwords($data->locations->pluck('location_name')->implode(', '))}}</p>
                            </td>

                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ucwords($data->supervisor ?
                                    $data->supervisor->name : '-')}}</p>
                            </td>

                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input" id="statusSwitch{{ $data->id }}" {{
                                        $data->status === 'active' ?
                                    'checked' : '' }}
                                    onchange="toggleEmployeeStatus({{ $data->id }},this.checked)">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="dropdown dropup">
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                        id="actionsDropdown{{ $data->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $data->id }}">
                                        <li><a class="dropdown-item" href="#"
                                                onclick="showActionCard({{ $data->id }})">Edit</a></li>
                                        <li><a class="dropdown-item" href="#"
                                                onclick="showActionCard({{ $data->id }})">Transfer</a></li>
                                        <li><a class="dropdown-item text-danger" href="#"
                                                onclick="showActionCard({{ $data->id }})">Delete</a></li>
                                    </ul>
                                </div>

                                <!-- Action Card (Initially Hidden) -->
                                <div id="actionCard{{ $data->id }}" class="position-absolute"
                                    style="display: none; z-index: 1050;">
                                    <div class="card shadow-sm" style="width: 18rem;">
                                        <div class="card-body">
                                            <h5 class="card-title">Employee Actions</h5>
                                            <button class="btn btn-primary btn-sm"
                                                onclick="editEmployee({{ $data->id }})">Edit</button>
                                            <button class="btn btn-warning btn-sm"
                                                onclick="transferEmployee({{ $data->id }})">Transfer</button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteEmployee({{ $data->id }})">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </td>


                            {{-- <td class="align-middle">
                                <a href="{{route('user.edit',$data->id)}}" class="btn btn-dark btn-sm mt-3">
                                    Edit
                                </a>
                            </td> --}}
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No User data found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            {{-- </div> --}}
        </div>
    </div>


    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel"
        aria-hidden="true">
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
                                <input type="text" class="form-control" name="name" id="employee_name"
                                    placeholder="Enter full name">
                                <div class="text-danger small" id="employee_name_error"></div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="employee_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" id="employee_email"
                                    placeholder="Enter email">
                                <div class="text-danger small" id="employee_email_error"></div>
                            </div>
                            <input type="text" name="fakeusernameremembered" style="display:none;">
                            <input type="password" name="fakepasswordremembered" style="display:none;">
                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="employee_password" class="form-label">Password *</label>
                                <input type="password" class="form-control" name="password" id="employee_password"
                                    placeholder="Enter password">
                                <div class="text-danger small" id="employee_password_error"></div>
                            </div>
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="employee_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" placeholder="Enter phone number"
                                    autocomplete="off">
                                <div class="text-danger small" id="employee_phone_error"></div>
                            </div>



                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="employee_role" class="form-label">Role *</label>
                                <select class="form-select" name="role" id="employee_role">
                                    <option value="">Select role</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="employee">Employee</option>
                                    <option value="complaint">Complaint</option>
                                </select>
                                <div class="text-danger small" id="employee_role_error"></div>
                            </div>

                            <!-- Supervisor -->
                            <div class="col-md-6">
                                <label for="employee_supervisor" class="form-label">Supervisor</label>
                                <select class="form-select" name="supervisor_id" id="employee_supervisor">
                                    <option value="" selected hidden>Select Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">
                                        {{ $supervisor->name }} ({{ $supervisor->email }})
                                    </option>
                                    @endforeach
                                </select>
                                <div class="text-danger small" id="employee_supervisor_error"></div>
                            </div>


                            <!-- Zone -->
                            <div class="col-md-6">
                                <label for="employee_zone" class="form-label">Zone *</label>
                                <select class="form-select" name="zone_id" id="employee_zone">
                                    <option value="" selected hidden>Select zone</option>
                                    @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger small" id="employee_zone_error"></div>
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label for="employee_location" class="form-label">Location</label>
                                <select class="form-select" name="location_id" id="employee_location">
                                    <option value="" selected hidden>Select location</option>
                                    {{-- Here Location will get by select the zone --}}
                                </select>
                                <div class="text-danger small" id="employee_location_error"></div>
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


</div>
@endsection
@section('scripts')
<script>
    // Handle form submission
        $('#addEmployeeForm').submit(function(e) {
        e.preventDefault();
        $('.text-danger.small').text('');
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('zone.employee.store') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#addEmployeeModal').modal('hide'); 

                    // Reset form
                    $('#addEmployeeForm')[0].reset();

                    // SweetAlert success
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Update employee list dynamically (assuming you have a div or table with id #employeeList)
                    let newEmployeeHtml = `
                        <tr id="employee_${response.data.id}">
                            <td>${response.data.name}</td>
                            <td>${response.data.email}</td>
                            <td>${response.data.phone ?? '-'}</td>
                            <td>${response.data.role}</td>
                            <td>${response.data.supervisor_name ?? '-'}</td>
                            <td>${response.data.zone_name ?? '-'}</td>
                            <td>${response.data.location_name ?? '-'}</td>
                        </tr>
                    `;
                    $('#employeeList').prepend(newEmployeeHtml); // add to top of the list
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, messages) {
                        let errorFieldId = '';

                        if (key === 'zone_id') {
                            errorFieldId = '#employee_zone_error';
                        } else if (key === 'location_id') {
                            errorFieldId = '#employee_location_error';
                        } else if (key === 'supervisor_id') {
                            errorFieldId = '#employee_supervisor_error';
                        } else {
                            errorFieldId = '#employee_' + key + '_error';
                        }

                        $(errorFieldId).text(messages[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An unexpected error occurred. Please try again.'
                    });
                }
            }
        });
    });

    // Fetch locations based on selected zone
    let getLocationsUrl = "{{ route('zone.getLocations', ['id' => 'ZONE_ID_PLACEHOLDER']) }}";

    $('#employee_zone').change(function() {
        var zoneId = $(this).val();
        if (zoneId) {
            let url = getLocationsUrl.replace('ZONE_ID_PLACEHOLDER', zoneId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    console.log(data);
                    $('#employee_location').empty().append('<option value="" selected hidden>Select location</option>');
                    $.each(data.locations, function(key, location) {
                        $('#employee_location').append('<option value="' + location.id + '">' + location.location_name + '</option>');
                    });
                },
                error: function() {
                    alert('Error fetching locations. Please try again.');
                }
            });
        } else {
            $('#employee_location').empty().append('<option value="" selected hidden>Select location</option>');
        }
    });


    // Toggle employee status
    function toggleEmployeeStatus(employeeId, isActive) {
        let status = isActive ? 'active' : 'inactive';
        let url = "{{ route('zone.employee.status', ':id') }}".replace(':id', employeeId);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                employee_id: employeeId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An unexpected error occurred. Please try again.'
                });
            }
        });
    }

        function showActionCard(employeeId) {
        // Hide any previously shown action cards
        document.querySelectorAll('.action-card').forEach(card => card.style.display = 'none');

        // Show the selected action card
        const card = document.getElementById('actionCard' + employeeId);
        card.style.display = 'block';

        // Position the card above the button
        const button = document.getElementById('actionsDropdown' + employeeId);
        const rect = button.getBoundingClientRect();
        card.style.top = `${rect.top - card.offsetHeight - 10}px`;
        card.style.left = `${rect.left}px`;
    }

    // Optional: Close the card when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown') && !event.target.closest('.action-card')) {
        document.querySelectorAll('.action-card').forEach(card => card.style.display = 'none');
        }
    });
</script>

@endsection