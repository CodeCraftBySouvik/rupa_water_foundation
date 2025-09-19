@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
{{-- @include('layouts.navbars.auth.topnav', ['title' => 'Zone Wise Location Management']) --}}
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
                    <div class="col-auto">
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


                {{-- <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Sl.No</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Zone</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Location</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>

                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody id="locationsTableBody">
                                @forelse($locations as $index=> $data)
                                <tr class="text-center" id="location-row-{{ $data->id }}">
                                    <td>
                                        <div class="">
                                            <h6 class="mb-0 text-sm">{{$index + 1}}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ucwords($data->zone_name ?
                                            $data->zone_name->name : '')}}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">{{$data->location_details ?
                                            $data->location_details->title : '-'}}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input"
                                                id="statusSwitch{{ $data->id }}" {{ $data->status === 'Active' ?
                                            'checked' : '' }}
                                            onchange="toggleLocationStatus({{ $data->id }},this.checked)">
                                        </div>
                                    </td>

                                    <td class="align-middle">
                                        <a href="{{route('zone.location.index',['edit' => $data->id])}}" class=" "
                                            style="margin-right: 8px;">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="deleteLocation({{$data->id}})">
                                            <i class="fa fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        No Zone Location Found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $locations->links() }}
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        @forelse($locations->groupBy(function($data) {
                        return $data->zone_name ? $data->zone_name->name : 'Unknown Zone';
                        }) as $zoneName => $zoneLocations)
                        <h5 class="text-primary mt-4 ms-4">{{ $zoneName }}</h5>
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Sl.No</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Location</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zoneLocations as $index => $data)
                                <tr class="text-center" id="location-row-{{ $data->id }}">
                                    <td>
                                        <div class="">
                                            <h6 class="mb-0 text-sm">{{ $index + 1 }}</h6>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">
                                            {{ $data->location_details ? $data->location_details->title : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input"
                                                id="statusSwitch{{ $data->id }}" {{ $data->status === 'Active' ?
                                            'checked' : '' }}
                                            onchange="toggleLocationStatus({{ $data->id }},this.checked)">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('zone.location.index', ['edit' => $data->id]) }}" class=" "
                                            style="margin-right: 8px;">
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
                        @empty
                        <div class="text-center py-4">No Zone Location Found</div>
                        @endforelse

                        <div class="mt-3">
                            {{ $locations->links() }}
                        </div>
                    </div>
                </div> --}}
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
                                                <a href="{{ route('zone.location.index', ['edit' => $data->id]) }}"
                                                    style="margin-right: 8px;">
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
        {{-- --}}

        {{-- <div class="col-md-4">
            <form method="POST"
                action="{{ isset($editLocation) ? route('zone.location.update', $editLocation->id) : route('zone.location.store') }}">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <p class="text-uppercase text-sm">{{isset($editLocation) ? 'Edit' : 'Create'}} Zone Wise
                            Location</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Zone</label>
                                    <select name="zone_id" id="zone_id" class="form-control">
                                        <option value="" selected hidden>-- Select Zone --</option>
                                        @foreach ($getZones as $zone)
                                        <option value="{{ $zone->id }}" {{(isset($editLocation) && $editLocation->
                                            zone_id == $zone->id) ? 'selected' : ''}}>
                                            {{ $zone->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('zone_id')
                                    <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="location_id" class="form-control-label">Location</label>
                                    <select class="form-control select2-single" name="location_id" id="location_id">
                                        <option value="" selected hidden>Select Location</option>
                                        @foreach($getLocations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id', $editLocation->
                                            location_id ?? '') == $location->id ? 'selected' : '' }}>
                                            {{ $location->title }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('location_id')
                                    <p class="text-danger small">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm">{{isset($editLocation) ? 'Update' :
                                    'Create'}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div> --}}

    </div>
    {{-- @include('layouts.footers.auth.footer') --}}
</div>
@endsection
@section('scripts')
<script>
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