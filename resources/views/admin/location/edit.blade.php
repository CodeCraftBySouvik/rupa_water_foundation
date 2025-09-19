@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
{{-- @include('layouts.navbars.auth.topnav', ['title' => 'Edit Location']) --}}
    
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form role="form" method="POST" action="{{route('location.update')}}">
                    @csrf
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Edit Location</p>
                            <a href="{{route('location.index')}}" class="btn btn-primary btn-sm ms-auto">Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Location Id</label>
                                    <input class="form-control" type="text" name="location_id"
                                        placeholder="Enter Location Id" value="{{ old('location_id',$locationEdit->location_id ?? '') }}">
                                    @error('location_id')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Title</label>
                                    <input class="form-control" type="title" name="title" value="{{ old('title',$locationEdit->title ?? '') }}"
                                        placeholder="Enter Location Name">
                                    @error('title')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Location&nbsp;Type</label>

                                    <select name="location_type" class="form-control">
                                        <option value="" disabled {{ old('location_type', $locationEdit->location_type ??
                                            '') == '' ? 'selected' : '' }}>
                                            -- Select --
                                        </option>

                                        <option value="roadside" {{ old('location_type', $locationEdit->location_type ?? '')
                                            == 'roadside' ? 'selected' : '' }}>
                                            Roadside
                                        </option>

                                        <option value="complex" {{ old('location_type', $locationEdit->location_type ?? '')
                                            == 'complex' ? 'selected' : '' }}>
                                            Complex
                                        </option>
                                    </select>
                                    @error('location_type')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Latitude</label>
                                    <input class="form-control" type="text" name="latitude"
                                        value="{{ old('latitude',$locationEdit->latitude ?? '') }}">
                                    @error('latitude')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Longitude</label>
                                    <input class="form-control" type="text" name="longitude"
                                        value="{{ old('longitude',$locationEdit->longitude ?? '') }}">
                                    @error('longitude')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Position</label>

                                    <select name="position" class="form-control">
                                        <option value="" disabled {{ old('position',$locationEdit->position ?? '')===null ? 'selected' : '' }}>
                                            -- Select --
                                        </option>

                                        <option value="small" {{ old('position',$locationEdit->position ?? '')=='small' ? 'selected' : '' }}>
                                            Small
                                        </option>

                                        <option value="big" {{ old('position',$locationEdit->position ?? '')=='big' ? 'selected' : '' }}>
                                            Big
                                        </option>
                                    </select>
                                    @error('position')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Opening Date</label>
                                    <input class="form-control" type="date" name="opening_date"
                                        value="{{ old('opening_date',$locationEdit->opening_date) }}">
                                    @error('opening_date')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Address</label>
                                    <textarea class="form-control" name="address" placeholder="Enter Address"
                                        rows="3">{{ old('address',$locationEdit->address) }}</textarea>
                                    @error('address')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{$locationEdit->id}}">
                            <div class="col-md-6">
                               <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection