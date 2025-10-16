@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')

<section class="content">
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div id="alert">
                        @include('components.alert')
                    </div>
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Gallery List</h6>
                    </div>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('inspection.index')}}" class="btn btn-sm btn-primary">Back</a>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive p-0">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Sl No.</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Image</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($gallery as $index => $item)
                                    <tr id="gallery_section_{{$item->id}}">
                                        <td class="text-center">{{ $index + $gallery->firstItem() }}</td>
                                        <td>
                                            <div class="text-center">
                                                @if (!empty($item->image_path) &&
                                                file_exists(public_path($item->image_path)))
                                                <a href="{{ asset($item->image_path) }}" target="_blank">
                                                    <img src="{{ asset($item->image_path) }}" alt="image_gallery"
                                                        style="height: 50px; width: 70px; object-position: center;"
                                                        class="img-thumbnail mr-2">
                                                </a>
                                                @else
                                                <a href="{{ asset('assets/images/placeholder.jpg') }}" target="_blank">
                                                    <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                        alt="image-gallery" style="height: 50px" class="mr-2">
                                                    @endif
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('inspection.galleries.edit', $item->id) }}"
                                                    class="btn btn-dark btn-sm mt-1">
                                                    Edit
                                                </a>
                                                <a href="javascript: void(0)" class="btn btn-sm btn-danger mt-1"
                                                    onclick="deleteGallery({{$item->id}})">
                                                    Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">No records found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="pagination-container">
                                {{$gallery->appends($_GET)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                @if (!isset($editableGallery))
                {{-- Upload Gallery Form --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Upload Gallery</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inspection.galleries.Store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mt-2">
                                <label for="image">Image <span class="text-danger">*</span></label>
                                <input type="file" name="images[]" class="form-control" multiple>
                                @error('images')
                                <p class="text-danger small">{{ $message }}</p>
                                @enderror
                                @error('images.*') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                            <input type="hidden" name="inspection_id" value="{{ $inspection->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                        </form>
                    </div>
                </div>
                @else
                {{-- Edit Gallery Form --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Gallery</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inspection.galleries.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="gallery_id" value="{{ $editableGallery->id }}">
                            <input type="hidden" name="inspection_id" value="{{ $editableGallery->inspection_id }}">

                            <div class="form-group mt-2">
                                <label for="image">Replace Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control">
                                @error('image') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>

                            <div class="text-center mt-3">
                                <label>Current Image:</label><br>
                                @if (!empty($editableGallery->image_path) &&
                                file_exists(public_path($editableGallery->image_path)))
                                <img src="{{ asset($editableGallery->image_path) }}" class="img-thumbnail"
                                    style="height: 60px; width: 90px;">
                                @else
                                <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-thumbnail"
                                    style="height: 60px;">
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('inspection.galleries.list', ['inspection_id' => $editableGallery->inspection_id]) }}"
                                    class="btn btn-danger btn-sm">Back</a>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

@endsection