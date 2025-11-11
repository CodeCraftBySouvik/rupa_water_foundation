@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="{{ \App\Helpers\Helpers::isSupervisor() ? 'col-12' : 'col-8' }}">
            <div class="card mb-4">
                <div id="alert">
                    @include('components.alert')
                </div>
                <div class="card-header pb-0">
                    <h6>Gallery</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @foreach($galleryItems as $item)
                    
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3"> {{-- Bootstrap gap utility --}}
                                @foreach ($item->images as $index => $img)
                                @if (file_exists(public_path($img)))
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="position-relative rounded overflow-hidden shadow-sm">
                                        
                                            {{-- thumbnail --}}
                                            <a href="{{ asset($img) }}" class="glightbox" data-gallery="gallery-{{ $item->id }}">
                                                <img src="{{ asset($img) }}" class="img-fluid w-100 h-100 object-fit-cover"
                                                >
                                            </a>
                                       
                                        {{-- delete X button --}}
                                        <form id="form-{{ $item->id }}-{{ $index }}" method="POST"
                                            action="{{ route('gallery.image.delete', [$item->id, $index]) }}"
                                            class="position-absolute top-0 end-0 m-1" style="z-index:10;">
                                            @csrf
                                            <button type="button"
                                                class="btn btn-danger btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center delete-image-button"
                                                data-form-id="form-{{ $item->id }}-{{ $index }}"
                                                style="width:24px;height:24px;line-height:0;">X</button>
                                        </form>

                                    </div>
                                </div>
                                 @endif
                                @endforeach
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @if (!\App\Helpers\Helpers::isSupervisor())
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
        @endif
    </div>
    {{-- @include('layouts.footers.auth.footer') --}}
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lightbox = GLightbox({
            selector: '.glightbox'
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