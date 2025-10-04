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
                             <div class="search-wrapper">
                            <input type="text" name="search" id="searchBox" value="{{ request('search') }}"
                                class="form-control form-control-sm mb-3" placeholder="Search..."
                                onkeyup="searchLocations(this.value)">
                             </div>
                        </form>

                        <!-- Import Button -->
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="fas fa-file-csv me-1"></i> Import
                        </button>
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



                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        @php
                        $groupedZones = $locations->groupBy(function($data) {
                        return $data->zone_name ? $data->zone_name->name : 'Unknown Zone';
                        });
                        $totalLocations = collect($groupedZones)->flatten()->count();
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
                                    {{ $zoneName }}
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
                                            <th>Status</th>
                                            <th>Action</th>
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