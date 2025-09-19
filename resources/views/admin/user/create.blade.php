@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
{{-- @include('layouts.navbars.auth.topnav', ['title' => 'Create User']) --}}
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form role="form" method="POST" action="{{route('user.store')}}">
                    @csrf
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <p class="mb-0">Create User</p>
                            <a href="{{route('user.index')}}" class="btn btn-primary btn-sm ms-auto">Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Name</label>
                                    <input class="form-control" type="name" name="name" value="{{ old('name') }}"
                                        placeholder="Enter Name">
                                    @error('name')
                                      <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email" value="{{ old('email') }}" >
                                    @error('email') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                            </div>
                           <div class="col-md-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Mobile</label>
                                    <input type="mobile" name="mobile" class="form-control" placeholder="Mobile Number" aria-label="Mobile" value="{{ old('mobile') }}" >
                                    @error('mobile') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label for="password" class="form-control-label">Password</label>
                                    <input type="text" id="password" name="password" class="form-control pr-5" placeholder="Password" aria-label="Password">
                                    
                                    @error('password') <p class="text-danger text-xs pt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            
                            <div class="col-md-6">
                               <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
