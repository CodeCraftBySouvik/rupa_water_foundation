@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
{{-- @include('layouts.navbars.auth.topnav', ['title' => 'About Us']) --}}
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div id="alert">
                    @include('components.alert')
                </div>
                <div class="card-header pb-0">
                    <h6>About Content Table</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Title</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($about)
                                <tr>
                                    <td style="white-space: normal; word-break: break-word;">
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                   {!! html_entity_decode($about->title) !!}
                                                </h6>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{strip_tags(Str::limit($about->description,60))}}
                                        </p>

                                    </td>
                                </tr>

                                @endif


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- --}}

        <div class="col-md-4">
            <form method="POST" action="{{route('about_us.update',$aboutUs->id)}}">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <p class="text-uppercase text-sm">About Us</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Title</label>
                                    <textarea class="form-control" id="title" name="title"
                                        value="">{{ old('title', $aboutUs?->title ?? '') }}</textarea>
                                    @error('title')
                                    <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Description</label>
                                    <textarea id="description" class="form-control" rows="4"
                                        name="description">{{ old('description', $aboutUs?->description ?? '') }}</textarea>
                                    @error('description')
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
@section('scripts')
<script>
    ClassicEditor
            .create(document.querySelector('#title'))
            .catch(error => {
                console.error(error);
            });
    ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
</script>

@endsection