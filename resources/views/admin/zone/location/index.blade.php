@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div id="alert">
                    @include('components.alert')
                    @if(session('import_errors'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Zone Wise Location</h6>
                    <div class="d-flex align-items-center">
                        <!-- 🔍 Search Bar -->
                        <form method="GET" action="{{ route('zone.location.index') }}" class="d-flex me-2">
                            <div class="search-wrapper me-2">
                                <input type="text" name="search" id="searchBox" value="{{ request('search') }}"
                                    class="form-control form-control-sm mb-3" placeholder="Search...">
                            </div>
                            <div>
                                <a href="{{route('zone.location.index')}}" class="btn btn-primary btn-sm"
                                    id="refreshBtn"> <i class="fa fa-refresh"></i></a>
                            </div>
                        </form>
                        @if(!\App\Helpers\Helpers::isSupervisor())
                        <!-- Import Button -->
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="fas fa-file-csv me-1"></i> Import
                        </button>
                        {{-- <a href="{{ route('zone.location.export') }}" class="btn btn-outline-primary btn-sm ms-2">
                            <i class="fa fa-download"></i> Export CSV
                        </a> --}}
                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" data-bs-toggle="modal"
                            data-bs-target="#addLocationModal">
                            <i class="fas fa-plus"></i> Add Location
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" data-bs-toggle="modal"
                            data-bs-target="#addZoneModal">
                            + Add Zone
                        </button>
                        @endif
                    </div>
                </div>
                {{-- Import Modal --}}
                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="importModalLabel">Import CSV File</h5>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form method="POST" action="{{ route('zone.location.import') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Upload CSV File</label>
                                        <input type="file" name="file" class="form-control">
                                        @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> Import
                                        </button>

                                        <button type="button"
                                            onclick="window.location='{{ route('zone.location.sample') }}'"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-csv me-1"></i> Sample CSV Download
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Location Modal --}}
                <div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <form id="addLocationForm" method="POST" action="{{ route('zone.location.store') }}">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addLocationModalLabel">Add New Zone Location</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-4">
                                    <div class="row g-3">
                                        <!-- Zone -->
                                        <div class="col-12">
                                            <label for="zone_id" class="form-label">Zone *</label>
                                            <select class="form-select" name="zone_id" id="zone_id" required>
                                                <option value="" selected hidden>Select Zone</option>
                                                @foreach($getZones as $zone)
                                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger small" id="zone_id_error"></div>
                                        </div>

                                        <!-- Location Name -->
                                        <div class="col-12">
                                            <label for="location_name" class="form-label">Location Name *</label>
                                            <input type="text" class="form-control" name="location_name"
                                                id="location_name" placeholder="Enter location" required>
                                            <div class="text-danger small" id="location_name_error"></div>
                                        </div>

                                        <!-- Position -->
                                        <div class="col-12">
                                            <label for="position" class="form-label">Position</label>
                                            <input type="text" class="form-control" name="position" id="position"
                                                placeholder="Enter position">
                                            <div class="text-danger small" id="position_error"></div>
                                        </div>

                                        <!-- Opening Date -->
                                        <div class="col-12">
                                            <label for="opening_date" class="form-label">Opening Date</label>
                                            <input type="date" class="form-control" name="opening_date"
                                                id="opening_date">
                                            <div class="text-danger small" id="opening_date_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Add Location</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Add Zone Modal --}}
                <div class="modal fade" id="addZoneModal" tabindex="-1" role="dialog"
                    aria-labelledby="addZoneModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form id="addZoneForm">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addZoneModalLabel">Add New Zone</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="zone_name" class="form-control-label">Zone Name</label>
                                        <input type="text" class="form-control" name="name" id="zone_name">
                                        <div class="text-danger small" id="zone_name_error"></div>
                                    </div>


                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Save Zone</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        {{-- @php
                        $groupedZones = $locations->groupBy(function($data) {
                        return $data->zone_name ? $data->zone_name->name : 'Unknown Zone';
                        });
                        $totalLocations = collect($groupedZones)->flatten()->count();
                        @endphp --}}

                        @php
                            $totalLocations = $getZones->sum(fn($zone) => $zone->zoneLocations->count());
                            $groupedZones = $getZones->mapWithKeys(function($zone) {
                            return [$zone->name => $zone->zoneLocations];
                            });
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2 ms-2">
                            <h6>Total Locations: <span class="badge bg-primary">{{ $totalLocations }}</span></h6>
                        </div>
                        {{-- Zone Tabs --}}
                        <ul class="nav nav-tabs" id="zoneTabs" role="tablist">
                            @foreach($groupedZones as $zoneName => $zoneLocations)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                    id="tab-{{ Str::slug($zoneName) }}" data-bs-toggle="tab"
                                    data-bs-target="#content-{{ Str::slug($zoneName) }}" type="button" role="tab">
                                    {{ ucwords($zoneName) }}
                                    <span class="badge bg-secondary">{{ count($zoneLocations) }}</span>
                                </button>
                            </li>
                            @endforeach
                        </ul>

                        {{-- Zone Content --}}
                        <div class="tab-content mt-3" id="zoneTabContent">
                            @foreach($groupedZones as $zoneName => $zoneLocations)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="content-{{ Str::slug($zoneName) }}" role="tabpanel">

                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Sl.No</th>
                                            <th>Location</th>
                                            <th>Position</th>
                                            <th>Opening Date</th>
                                            @if(!\App\Helpers\Helpers::isSupervisor())
                                            <th>Status</th>
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($zoneLocations as $index => $data)
                                        <tr class="text-center" id="location-row-{{ $data->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-sm bg-gradient-info">
                                                    {{ $data->location_details ? $data->location_details->title : '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-sm bg-gradient-success">
                                                    {{ $data->location_details ? $data->location_details->position : '-'
                                                    }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-sm bg-gradient-success">
                                                    {{ $data->location_details ? $data->location_details->opening_date :
                                                    '-' }}
                                                </span>
                                            </td>
                                            @if(!\App\Helpers\Helpers::isSupervisor())
                                            <td>
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="statusSwitch{{ $data->id }}" {{ $data->status === 'Active' ?
                                                    'checked' : '' }}
                                                    onchange="toggleLocationStatus({{ $data->id }}, this.checked)">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="editZoneLocation({{ $data->id }})" data-bs-toggle="modal"
                                                    data-bs-target="#editZoneLocationModal" style="margin-right: 8px;">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="deleteLocation({{ $data->id }})">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
        {{--Edit Location Modal --}}
        <div class="modal fade" id="editZoneLocationModal" tabindex="-1" aria-labelledby="editZoneLocationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form id="editZoneLocationForm">
                    @csrf
                    <input type="hidden" name="edit_location_id" id="edit_location_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Zone Location</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <!-- Zone -->
                                <div class="col-12">
                                    <label for="edit_zone_id" class="form-label">Zone *</label>
                                    <select class="form-select" name="zone_id" id="edit_zone_id" required>
                                        <option value="" selected hidden>Select Zone</option>
                                        @foreach($getZones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger small" id="edit_zone_id_error"></div>
                                </div>
                                <!-- Location -->
                                <div class="col-12">
                                    <label for="edit_location_name" class="form-label">Location *</label>
                                    <input type="text" class="form-control" name="location_id" id="edit_location_name"
                                        placeholder="Enter location" required>
                                    <div class="text-danger small" id="edit_location_id_error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function () {
       $('#addZoneForm').on('submit', function(e){
          e.preventDefault();
            $('#zone_name_error').text('');

            $.ajax({
                url: "{{ route('zone.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (response) {
                     Swal.fire({
                        icon: 'success',
                        title: 'Zone added successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() =>{
                        // window.location.href = response.redirect;
                        location.reload();
                    });

                      // Reset the form fields
                    $('#addZoneForm')[0].reset();
                    $('#addZoneModal').modal('hide');

                     // --- Add new tab dynamically ---
                    let zoneSlug = response.name.toLowerCase().replace(/\s+/g, '-');
                    let newTab = `
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-${zoneSlug}" data-bs-toggle="tab" data-bs-target="#content-${zoneSlug}" type="button" role="tab">
                                ${response.name}
                                <span class="badge bg-secondary">0</span>
                            </button>
                        </li>
                    `;
                    $('#zoneTabs').append(newTab);

                    $('#zonesList .no-zones-message').remove();
                     let newZoneHtml = `
                            <div class="col-md-4 mb-4" id="zone-card-${response.id}">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title"><i class="fas fa-building"></i> ${response.name} </h5>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2" style="cursor: pointer;" id="zone-status-${response.id}" onclick="toggleZoneStatus(${response.id})">Active</span>
                                                <a data-bs-toggle="modal" data-bs-target="#editZoneModal" class="d-flex justify-content-center align-items-center border rounded p-1" style="width:32px; height:32px; cursor:pointer;" onclick="editZone(${response.id})">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <div><h4 class="text-primary">${response.zone_employees_count ?? 0}</h4><small>Employees</small></div>
                                            <div><h4 class="text-primary">${response.zone_locations_count ?? 0}</h4><small>Locations</small></div>
                                        </div>

                                        <div class="mt-3 d-flex justify-content-between">
                                            <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm" onclick="viewLocations(${response.id})">
                                                <i class="fas fa-map-marker-alt"></i> View Locations
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm" onclick="viewEmployees(${response.id})">
                                                <i class="fas fa-users"></i> View Employees
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        $('#zonesList').append(newZoneHtml);
                         
                }, 
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#zone_name_error').text(errors.name[0]);
                    }
                   
                }
            });
       }); 
    });
</script>
<script>
    $('#addLocationForm').submit(function(e) {
    e.preventDefault();
    $('.text-danger.small').text(''); // Clear previous errors
    let formData = $(this).serialize();

    $.ajax({
        url: "{{ route('zone.location.store') }}",
        type: 'POST',
        data: formData,
        success: function(response) {
            if(response.success) {
                $('#addLocationModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Location Added!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Or dynamically add row to table
                });
            }
        },
        error: function(xhr) {
            if(xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if(errors.zone_id) $('#zone_id_error').text(errors.zone_id[0]);
                if(errors.location_name) $('#location_name_error').text(errors.location_name[0]);
                if(errors.position) $('#position_error').text(errors.position[0]);
                if(errors.opening_date) $('#opening_date_error').text(errors.opening_date[0]);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.'
                });
            }
        }
    });
});
</script>
<script>
    function editZoneLocation(id) {
        let url = "{{ route('zone.location.edit', ':id') }}".replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log(response);
                if(response) {
                    $('#edit_location_id').val(response.id);
                    $('#edit_zone_id').val(response.zone_id);
                    $('#edit_location_name').val(response.location_name);
                    $('#editZoneLocationModal').modal('show');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to fetch data!'
                });
            }
        });
    }

    $('#editZoneLocationForm').submit(function(e) {
    e.preventDefault();
    $('.text-danger.small').text('');

    let id = $('#edit_location_id').val();
    let url = "{{ route('zone.location.update', ':id') }}".replace(':id', id);
    let data = $(this).serialize();

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function(response) {
            if(response.success) {
                $('#editZoneLocationModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Or update the row dynamically
                });
            }
        },
        error: function(xhr) {
            if(xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if(errors.zone_id) $('#edit_zone_id_error').text(errors.zone_id[0]);
                if(errors.location_id) $('#edit_location_id_error').text(errors.location_id[0]);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.'
                });
            }
        }
    });
});



    $(document).ready(function() {
        $('#location_id').select2({
            placeholder: "Select Location",
            allowClear: true,
            width: '100%'
        });
    });

    function toggleLocationStatus(locationId,isChecked){
         let status = isChecked ? 'Active' : 'Inactive';
          let url = "{{ route('zone.location.status', ':id') }}".replace(':id', locationId);
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status updated!',
                    showConfirmButton: false,
                    timer: 1200
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'An error occurred.',
                });
                $('#statusSwitch' + locationId).prop('checked', !isChecked);
            }
        });
    }


    // Delete Location
    function deleteLocation(locationId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('zone.location.delete', ':id') }}".replace(':id', locationId);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Location deleted!',
                            showConfirmButton: false,
                            timer: 1200
                        });
                         $('#location-row-' + locationId).remove();
                         // Check if there are any remaining location rows
                        if ($('#locationsTableBody tr').length === 0) {
                            $('#locationsTableBody').append(`
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        No Zone Location Found
                                    </td>
                                </tr>
                            `);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'An error occurred.',
                        });
                    }
                });
            }
        });
    }
</script>
@endsection