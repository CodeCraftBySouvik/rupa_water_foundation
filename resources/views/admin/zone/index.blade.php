@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

@include('layouts.navbars.auth.topnav', ['title' => 'Zones'])

<div class="container-fluid py-4">
    <div id="alert">
        @include('components.alert')
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="font-weight-bolder text-white mb-0"></h6>
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addZoneModal">
            + Add Zone
        </button>
    </div>

    <!-- Add Zone Modal -->
    <div class="modal fade" id="addZoneModal" tabindex="-1" role="dialog" aria-labelledby="addZoneModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="addZoneForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addZoneModalLabel">Add New Zone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="zone_name" class="form-control-label">Zone Name</label>
                            <input type="text" class="form-control" name="name" id="zone_name">
                            <div class="text-danger small" id="zone_name_error"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-control-label">Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                            <div class="text-danger small" id="description_error"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Zone</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row" id="zonesList">
        {{-- @if (count($zones)>0) --}}
        @forelse($zones as $index => $zone)
        <div class="col-md-6 mb-4" id="zone-card-{{ $zone->id }}">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title"> <i class="fas fa-building"></i> {{$zone->name}} </h5>

                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2" style="cursor: pointer;" id="zone-status-{{$zone->id}}"
                                onclick="toggleZoneStatus({{$zone->id}})">Active</span>
                            <a data-bs-toggle="modal" data-bs-target="#editZoneModal"
                                class="d-flex justify-content-center align-items-center border rounded p-1"
                                style="width:32px; height:32px; cursor:pointer;" onclick="editZone({{$zone->id}})">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>

                    </div>
                    {{-- <h6 class="card-subtitle text-muted">Zone Code: ZONE001</h6> --}}
                    @if ($zone->description)
                    <p class="card-text mt-2">{{$zone->description}}</p>
                    @else
                    <p class="card-text mt-2">No description available.</p>
                    @endif
                    <p class="d-flex justify-content-between mb-0">

                        <strong><i class="ni ni-single-02 text-dark text-sm opacity-10"></i> Supervisor:</strong>
                        <span><strong>Michael Chen</strong></span>
                    </p>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <h4 class="text-primary">312</h4>
                            <small>Employees</small>
                        </div>
                        <div>
                            <h4 class="text-primary">{{$zone->zone_locations_count}}</h4>
                            <small>Locations</small>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm"
                            onclick="viewLocations({{ $zone->id }})">
                            <i class="fas fa-map-marker-alt"></i> View Locations
                        </a>
                        <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm"
                            onclick="viewEmployees({{ $zone->id }})">
                            <i class="fas fa-users"></i> View Employees
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card shadow-sm no-zones-message">
            <div class="card-body">
                <h5 class="text-center">No Zones Found</h5>
                <p class="text-center">It seems there are no zones available at the moment.</p>
            </div>
        </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $zones->links() }}
        </div>
        <!-- Edit Zone Modal -->
        <div class="modal fade" id="editZoneModal" tabindex="-1" role="dialog" aria-labelledby="editZoneModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="editZoneForm">
                    @csrf
                    <input type="hidden" name="zone_id" id="edit_zone_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editZoneModalLabel">Edit Zone</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="edit_zone_name" class="form-control-label">Zone Name</label>
                                <input type="text" class="form-control" name="name" id="edit_zone_name">
                                <div class="text-danger small" id="edit_zone_name_error"></div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_description" class="form-control-label">Description</label>
                                <textarea class="form-control" name="description" id="edit_description"></textarea>
                                <div class="text-danger small" id="edit_description_error"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm">Update Zone</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Locations Modal --}}
    <div class="modal fade" id="viewLocationsModal" tabindex="-1" aria-labelledby="viewLocationsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Zone Locations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul id="locationsList" class="list-group">
                        <!-- Location items will be appended dynamically here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="row">
        <div class="col-md-4">
            <form method="POST" action="{{ route('zone.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-sm mb-3">Create Zone</h6>

                        <div class="form-group mb-3">
                            <label for="zone_name" class="form-control-label">Zone Name</label>
                            <input type="text" class="form-control" name="zone_name" value="{{ old('zone_name') }}">
                            @error('zone_name')
                            <p class="text-danger small">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-control-label">Description</label>
                            <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-danger small">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}

</div>
@endsection

@section('scripts')
<script>
    function viewLocations(zoneId) {
    let url = "{{ route('zone.getLocations', ':id') }}".replace(':id', zoneId);

    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            $('#locationsList').empty();

            if (response.locations.length > 0) {
                response.locations.forEach(function(location) {
                    $('#locationsList').append(`
                        <li class="list-group-item">
                            <strong>${location.location_name}</strong> - 
                            <span class="badge ${location.status === 'Active' ? 'bg-success' : 'bg-danger'}">${location.status}</span>
                        </li>
                    `);
                });
            } else {
                $('#locationsList').append(`<li class="list-group-item">No locations found.</li>`);
            }

            $('#viewLocationsModal').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Unable to fetch locations'
            });
        }
    });
}

</script>
<script>
    $(document).ready(function () {
    $('#editZoneForm').on('submit', function(e){
        e.preventDefault();
        $('#edit_zone_name_error').text('');
        $('#edit_description_error').text('');

        $.ajax({
            url: "{{ route('zone.update') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });

                $('#editZoneForm')[0].reset();
                $('#editZoneModal').modal('hide');

                // Update the zone card in DOM
                let updatedZoneHtml = `
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title"><i class="fas fa-building"></i> ${response.zone.name} </h5>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2" style="cursor: pointer;" id="zone-status-${response.zone.id}" onclick="toggleZoneStatus(${response.zone.id})">Active</span>
                                    <a data-bs-toggle="modal" data-bs-target="#editZoneModal" class="d-flex justify-content-center align-items-center border rounded p-1" style="width:32px; height:32px; cursor:pointer;" onclick="editZone(${response.zone.id})">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                            <p class="card-text mt-2">${response.zone.description || 'No description available.'}</p>
                            <p class="d-flex justify-content-between mb-0">
                                <strong><i class="ni ni-single-02 text-dark text-sm opacity-10"></i> Supervisor:</strong>
                                <span><strong>Michael Chen</strong></span>
                            </p>
                            <div class="d-flex justify-content-between mt-3">
                                <div><h4 class="text-primary">312</h4><small>Employees</small></div>
                                <div><h4 class="text-primary">${response.location_count}</h4><small>Locations</small></div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between">
                                <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm" onclick="viewLocations(${response.zone.id})">
                                    <i class="fas fa-map-marker-alt"></i> View Locations
                                </a>
                                <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-users"></i> View Employees</a>
                            </div>
                        </div>
                    </div>
                `;

                $('#zone-card-' + response.zone.id).html(updatedZoneHtml);
                $('#zone-location-count-' + response.zone.id).text(response.location_count);

               

                // // Open the modal
                // $('#viewLocationsModal').modal('show');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors.name) {
                    $('#edit_zone_name_error').text(errors.name[0]);
                }
                if (errors.description) {
                    $('#edit_description_error').text(errors.description[0]);
                }
            }
        });
    });
});

</script>
<script>
    function editZone(zoneId) {
    let url = "{{ route('zone.edit', ':id') }}";
    url = url.replace(':id', zoneId);

    $.ajax({
        url: url, 
        method: 'GET',
        success: function(response) {
            $('#edit_zone_id').val(response.id);
            $('#edit_zone_name').val(response.name);
            $('#edit_description').val(response.description || '');
            $('#editZoneModal').modal('show');
        },
       error: function (xhr) {
            let errors = xhr.responseJSON.errors;
            if (errors.name) {
                $('#edit_zone_name_error').text(errors.name[0]);
            }
            if (errors.description) {
                $('#edit_description_error').text(errors.description[0]);
            }
        }
    });
}

</script>
<script>
    function toggleZoneStatus(zoneId){
        $.ajax({
            url: '/zone/toggle-status/' + zoneId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response){
                let statusBadge = $('#zone-status-' + zoneId);
                if(response.status === 'Active'){
                    statusBadge.removeClass('bg-danger').addClass('bg-success').text('Active');
                } else {
                    statusBadge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                }
            },
            error: function(){
                alert('An error occurred while toggling the zone status.');
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
       $('#addZoneForm').on('submit', function(e){
          e.preventDefault();
            $('#zone_name_error').text('');
            $('#description_error').text('');

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
                    });

                      // Reset the form fields
                    $('#addZoneForm')[0].reset();
                    $('#addZoneModal').modal('hide');

                    // Remove the "No Zones Found" message if it exists
                    $('#zonesList .no-zones-message').remove();
                     let newZoneHtml = `
                            <div class="col-md-6 mb-4" id="zone-card-${response.id}">
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
                                        <p class="card-text mt-2">${response.description || 'No description available.'}</p>
                                        <p class="d-flex justify-content-between mb-0">
                                            <strong><i class="ni ni-single-02 text-dark text-sm opacity-10"></i> Supervisor:</strong>
                                            <span><strong>Michael Chen</strong></span>
                                        </p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <div><h4 class="text-primary">312</h4><small>Employees</small></div>
                                            <div><h4 class="text-primary">45</h4><small>Locations</small></div>
                                        </div>

                                        <div class="mt-3 d-flex justify-content-between">
                                            <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-map-marker-alt"></i> View Locations</a>
                                            <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fas fa-users"></i> View Employees</a>
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
                    if (errors.description) {
                        $('#description_error').text(errors.description[0]);
                    }
                }
            });
       }); 
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.delete-image-button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const formId = this.dataset.formId;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This image will be deleted permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });
    });
</script>
@endsection