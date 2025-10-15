@extends('layouts.app')

@section('content')
<div class="container position-sticky z-index-sticky top-0">
    <div class="row">
        <div class="col-12">
            @include('layouts.navbars.guest.navbar')
        </div>
    </div>
</div>
<main class="main-content  mt-0">
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
                        <div class="col-xl-4 col-lg-5 col-md-7">
                            <div class="card card-plain shadow">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="">Change your Profile Credentials</h4>
                                    {{-- <p class="mb-0">Set a new password for your email</p> --}}
                                </div>
                                <div class="card-body">
                                    <form role="form" method="POST" action="{{ route('change.password.update') }}">
                                        @csrf

                                        <div class="flex flex-col mb-3">
                                            <input type="email" name="email" class="form-control form-control-lg"
                                                placeholder="Email" value="{{ old('email') }}" aria-label="Email">
                                            @error('email') <p class="text-danger text-xs pt-1"> {{$message}} </p>
                                            @enderror
                                        </div>
                                        <div class="flex flex-col mb-3">
                                            <input type="password" name="password" class="form-control form-control-lg"
                                                placeholder="New Password" aria-label="Password" autocomplete="new-password">
                                            @error('password') <p class="text-danger text-xs pt-1"> {{$message}} </p>
                                            @enderror
                                        </div>
                                        <div class="flex flex-col mb-3">
                                            <input type="password" name="password_confirmation"
                                                class="form-control form-control-lg" placeholder="Confirm Password"
                                                aria-label="Password" autocomplete="new-password">
                                            @error('password_confirmation') <p class="text-danger text-xs pt-1">
                                                {{$message}} </p>@enderror
                                        </div>
                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Reset Profile Credentials</button>
                                        </div>
                                    </form>
                                </div>
                                <div id="alert">
                                    @include('components.alert')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection