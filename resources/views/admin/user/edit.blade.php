@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'User Edit Form'])

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form role="form" method="POST" action="{{ route('user.update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Edit User</p>
                            <a href="{{ route('user.index') }}" class="btn btn-primary btn-sm ms-auto">Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Name</label>
                                    <input class="form-control" type="text" name="name" 
                                        value="{{ old('name', $user->name) }}" placeholder="Enter Name">
                                    @error('name')
                                      <p class="text-danger small">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email"
                                        value="{{ old('email', $user->email) }}">
                                    @error('email')
                                      <p class="text-danger text-xs pt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Mobile</label>
                                    <input type="text" name="mobile" class="form-control" placeholder="Mobile Number"
                                        value="{{ old('mobile', $user->mobile) }}">
                                    @error('mobile')
                                      <p class="text-danger text-xs pt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
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