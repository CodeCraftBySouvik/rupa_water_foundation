@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Zone Wise Location Management'])
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div id="alert">
                    @include('components.alert')
                </div>
                <div class="card-header pb-0">
                    <h6>Zone Wise Location</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
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
                            {{-- <tbody>
                                @forelse($userlist as $key=> $data)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{$key + 1}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ucwords($data->name)}}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">{{$data->mobile}}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{$data->email}}</span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{route('user.edit',$data->id)}}" class="btn btn-dark btn-sm mt-1">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        No User data found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- --}}

        <div class="col-md-4">
            <form method="POST" action="">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <p class="text-uppercase text-sm">About Us</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Zone</label>
                                    <select name="zone_id" id="zone_id" class="form-control">
                                        <option value="">-- Select Zone --</option>
                                        @foreach ($getZones as $zone)
                                         <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('zone_id')
                                    <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Location</label>
                                    <input type="text" class="form-control" name="location_name" value="{{ old('location_name') }}">
                                    @error('location_name')
                                    <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    {{-- @include('layouts.footers.auth.footer') --}}
</div>
@endsection