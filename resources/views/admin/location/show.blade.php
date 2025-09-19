@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
{{-- @include('layouts.navbars.auth.topnav', ['title' => 'Locations Details']) --}}

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12">

            <div class="card">

                <!-- Card Header -->
                <div class="card-header mb-3">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <h4 class="fw-bold mb-0">Location Details</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('location.index') }}" class="btn btn-sm btn-danger">
                                <i class="tf-icons ri-arrow-left-line"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Location ID:</span>
                            <div>{{ $data->location_id ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Title:</span>
                            <div>{{ $data->title ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Location Type:</span>
                            <div>{{ $data->location_type ? ucfirst($data->location_type) : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Latitude:</span>
                            <div>{{ $data->latitude ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Longitude:</span>
                            <div>{{ $data->longitude ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Position:</span>
                            <div>{{ $data->position ? ucfirst($data->position) : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Opening Date:</span>
                            <div>{{ $data->opening_date ? date('d-m-Y', strtotime($data->opening_date)) : 'N/A' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <span class="h6 me-1 label-color">Address:</span>
                            <div>{{ $data->address ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection