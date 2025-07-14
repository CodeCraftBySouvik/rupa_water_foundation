@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Gallery'])
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div id="alert">
                    @include('components.alert')
                </div>
                <div class="card-header pb-0">
                    <h6>Gallery</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @foreach($galleryItems as $item)
                    @php
                    $images = explode(',', $item->image_path); // Convert comma string to array
                    @endphp

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3"> {{-- Bootstrap gap utility --}}
                                @foreach($images as $index => $img)
                                <div class="col-md-3 col-sm-4 col-6">

                                    {{-- each thumbnail gets a relative wrapper --}}
                                    <div class="position-relative rounded overflow-hidden shadow-sm">

                                        {{-- thumbnail --}}
                                        <img src="{{ asset($img) }}" class="img-fluid w-100 h-100 object-fit-cover" alt="Gallery image {{ $index+1 }}">

                                        {{--  delete / close button --}}
                                        <form id="form-{{ $item->id }}-{{ $index }}" method="POST"
                                            action="{{route('gallery.image.delete',[$item->id,$index])}}"
                                            class="position-absolute top-0 end-0 m-1">
                                            @csrf

                                            <button type="submit" onclick="return confirm('Delete this image?')"
                                                class="btn btn-danger btn-sm d-flex align-items-center justify-content-center p-0"
                                                style="width:24px;height:24px;line-height:0;">
                                                {{-- <i class="fa fa-times text-white small"></i> --}}
                                            {{-- </button> --}}

                                            <button type="button" class="btn btn-danger btn-sm delete-image-button"
                                                data-form-id="form-{{ $item->id }}-{{$index}}" style="width:24px;height:24px;line-height:0;">
                                                X</button>
                                            </button>
                                        </form>

                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <form method="POST" action="{{route('gallery.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <p class="text-uppercase text-sm">Gallery</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="form-control-label">Image</label>
                                    <input class="form-control" type="file" name="image[]" multiple>
                                    @error('image')
                                    <p class="text-danger small">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
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
