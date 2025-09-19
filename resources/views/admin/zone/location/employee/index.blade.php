@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

{{-- @include('layouts.navbars.auth.topnav', ['title' => 'Zones']) --}}

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
            <div class="table-responsive">
                <table class="table align-items-center">
                    <thead>
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sl.No</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Name
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mobile
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zone
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
                                <p class="text-xs font-weight-bold mb-0">{{ucwords($data->mobile)}}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ucwords($data->role)}}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    {{ucwords($data->zones->pluck('name')->implode(', '))}}
                                </p>
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
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <div class="dropdown dropup">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="actionsDropdown{{ $data->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $data->id }}">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="transferEmployee({{ $data->id }})" data-bs-toggle="modal"
                                                    data-bs-target="#transferModal{{ $data->id }}">Transfer</a></li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="editEmployee({{ $data->id }})" data-bs-toggle="modal"
                                                    data-bs-target="#editEmployeeModal">Edit</a>
                                            </li>

                                            <li><a class="dropdown-item text-danger" href="#"
                                                    onclick="deleteEmployee({{ $data->id }})">Delete</a></li>
                                        </ul>
                                    </div>
                                    {{-- Show complaint button only if user has complaints --}}
                                    @if ($data->complaints->isNotEmpty())
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            data-bs-toggle="modal" data-bs-target="#complaintModal{{ $data->id }}">
                                            Complaint
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            {{-- Complaint Modal --}}
                            <div class="modal fade" id="complaintModal{{ $data->id }}" tabindex="-1"
                                aria-labelledby="complaintModalLabel{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="complaintModalLabel{{ $data->id }}">
                                                Complaint Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @foreach($data->complaints as $complaint)
                                            <div class="mb-3 p-2 border rounded">
                                                <p><strong>Description:</strong> {{ $complaint->description }}</p>
                                                <p><strong>Date:</strong> {{ $complaint->created_at->format('d-m-Y H:i')
                                                    }}</p>
                                                <div class="d-flex flex-wrap">
                                                    @php $images = json_decode($complaint->images, true); @endphp
                                                    @if($images)
                                                    @foreach($images as $img)
                                                    <img src="{{ asset($img) }}" alt="Complaint Image"
                                                        class="img-thumbnail me-2 mb-2"
                                                        style="width: 100px; height: 100px;">
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Transfer Modal --}}
                            <div class="modal fade" id="transferModal{{ $data->id }}" tabindex="-1"
                                aria-labelledby="transferModalLabel{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="transferModalLabel{{ $data->id }}">
                                                Transfer Employee
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <form method="POST" action="">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Request transfer for <strong>{{ $data->name }}</strong> to a
                                                    different zone.</p>

                                                {{-- From Zone --}}
                                                <div class="mb-3">
                                                    <label class="form-label">From Zone</label>
                                                    <select class="form-select" name="from_zone" disabled>
                                                        <option>{{ optional($data->zones->first())->name ?? 'Not
                                                            Assigned' }}</option>
                                                    </select>
                                                </div>

                                                {{-- To Zone --}}
                                                <div class="mb-3">
                                                    <label class="form-label">To Zone <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" name="to_zone" required>
                                                        <option value="">Select Zone</option>
                                                        @foreach($zones as $zone)
                                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- New Location --}}
                                                <div class="mb-3">
                                                    <label class="form-label">New Location</label>
                                                    <select class="form-select" name="new_location">
                                                        <option value="">Select new location</option>
                                                        {{-- You can populate dynamically based on zone --}}
                                                    </select>
                                                </div>

                                                {{-- Effective Date --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Effective Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="effective_date"
                                                        required>
                                                </div>

                                                {{-- Reason for Transfer --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Reason for Transfer</label>
                                                    <textarea class="form-control" name="reason" rows="3"
                                                        placeholder="Enter reason for transfer..."></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Submit Transfer
                                                    Request</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

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
            </div>
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

                            <!--Alternate Phone -->
                            <div class="col-md-6">
                                <label for="employee_alternate_number" class="form-label">Alternate Number</label>
                                <input type="text" class="form-control" name="alternate_number"
                                    placeholder="Enter Alternate number" autocomplete="off">
                                <div class="text-danger small" id="employee_alternate_number"></div>
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
                                <select class="form-select" name="zone_id[]" id="employee_zone" multiple
                                    style="width: 100%;">
                                    <option value="" selected hidden>Select zone</option>
                                    @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger small" id="employee_zone_error"></div>
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

    {{-- Edit Employee Modal --}}
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="editEmployeeForm">
                @csrf
                <input type="hidden" name="edit_employee_id" id="edit_employee_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="edit_employee_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="edit_name" id="edit_employee_name"
                                    placeholder="Enter full name">
                                <div class="text-danger small" id="edit_employee_name_error"></div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="edit_employee_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" name="edit_email" id="edit_employee_email"
                                    placeholder="Enter email">
                                <div class="text-danger small" id="edit_employee_email"></div>
                            </div>
                            <input type="text" name="fakeusernameremembered" style="display:none;">
                            <input type="password" name="fakepasswordremembered" style="display:none;">

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="edit_employee_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="edit_phone" placeholder="Enter phone number"
                                    autocomplete="off">
                                <div class="text-danger small" id="edit_employee_phone"></div>
                            </div>

                            <!--Alternate Phone -->
                            <div class="col-md-6">
                                <label for="edit_employee_alternate_number" class="form-label">Alternate Number</label>
                                <input type="text" class="form-control" name="edit_alternate_number"
                                    placeholder="Enter Alternate number" autocomplete="off">
                                <div class="text-danger small" id="edit_employee_alternate_number"></div>
                            </div>



                            <!-- Role -->
                            <div class="col-md-6">
                                <label for="edit_employee_role" class="form-label">Role *</label>
                                <select class="form-select" name="edit_role" id="edit_employee_role">
                                    <option value="">Select role</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="employee">Employee</option>
                                    <option value="complaint">Complaint</option>
                                </select>
                                <div class="text-danger small" id="edit_employee_role_error"></div>
                            </div>

                            <!-- Supervisor -->
                            <div class="col-md-6">
                                <label for="edit_employee_supervisor" class="form-label">Supervisor</label>
                                <select class="form-select" name="edit_supervisor_id" id="edit_employee_supervisor">
                                    <option value="" selected hidden>Select Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">
                                        {{ $supervisor->name }} ({{ $supervisor->email }})
                                    </option>
                                    @endforeach
                                </select>
                                <div class="text-danger small" id="edit_employee_supervisor_error"></div>
                            </div>


                            <!-- Zone -->
                            <div class="col-md-6">
                                <label for="edit_employee_zone" class="form-label">Zone *</label>
                                <select class="form-select" name="edit_zone_id[]" id="edit_employee_zone" multiple
                                    style="width: 100%;">
                                    <option value="" selected hidden>Select zone</option>
                                    @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger small" id="edit_employee_zone_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update Employee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
@section('scripts')
<script>
    $('#employee_zone').select2({
        allowClear: true,
        dropdownParent: $('#addEmployeeModal')
    });
    $('#edit_employee_zone').select2({
        allowClear: true,
        dropdownParent: $('#editEmployeeModal')
    });

     // Function to fetch employee data and populate edit form
    function editEmployee(id) {
        let url = "{{ route('zone.employee.edit', ':id') }}".replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                if (response.data) {
                    let employee = response.data;
                    
                    // Fill modal fields
                    $('#edit_employee_id').val(employee.id);
                    $('#edit_employee_name').val(employee.name);
                    $('#edit_employee_email').val(employee.email);
                    $('#edit_employee_phone').val(employee.mobile);
                    $('#edit_employee_alternate_number').val(employee.alternate_number);
                    $('#edit_employee_role').val(employee.role);
                    $('#edit_employee_supervisor').val(employee.supervisor_id);
                    
                    // Set zones (multiple select)
                    let zoneIds = employee.zone_id || [];
                    $('#edit_employee_zone').val(zoneIds).trigger('change');

                    // Open modal
                    $('#editEmployeeModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to fetch employee data.'
                });
            }
        });
    }

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
                console.log(response);
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
                    // Build new employee row HTML
                let newEmployeeHtml = `
                    <tr class="text-center" id="employee_${response.data.id}">
                        <td><h6 class="mb-0 text-sm">New</h6></td>
                        <td><p class="text-xs font-weight-bold mb-0">${response.data.name}</p></td>
                        <td><p class="text-xs font-weight-bold mb-0">${response.data.role}</p></td>
                        <td><p class="text-xs font-weight-bold mb-0">${response.data.zone_name ?? '-'}</p></td>
                        <td><p class="text-xs font-weight-bold mb-0">${response.data.supervisor_name ?? '-'}</p></td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input" id="statusSwitch${response.data.id}" ${response.data.status === 'active' ? 'checked' : ''} onchange="toggleEmployeeStatus(${response.data.id}, this.checked)">
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropup">
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="actionsDropdown${response.data.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="actionsDropdown${response.data.id}">
                                    <li><a class="dropdown-item" href="#" onclick="transferEmployee(${response.data.id})">Transfer</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="editEmployee(${response.data.id})">Edit</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteEmployee(${response.data.id})">Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;

                // Prepend new employee row
                $('#employeeList').append(newEmployeeHtml);
                location.reload();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, messages) {
                        let errorFieldId = '';

                        if (key === 'zone_id') {
                            errorFieldId = '#employee_zone_error';
                        }
                        else if (key === 'supervisor_id') {
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