@extends('admin.index')
@section('title')
    shahid Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit shahid</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.shahid.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit shahid</h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="{{ route('shahid.create') }}">Create</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('shahid.update', $shahid->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name" class="required">Shahid Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    value="{{ $shahid->name }}">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea required type="text" name="address" class="form-control" id="address" rows="3">{{ $shahid->address }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="editor1">Description</label>
                                <textarea required class="form-control" name="description" id="editor1" rows="10">{{ $shahid->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-control-file" for="thumbnail_path">Thumbnail Image</label>
                                <input name="thumbnail_path" type="file" class="form-control-file"
                                    id="thumbnail_path">
                            </div>
                            <img src="{{ get_file($shahid->thumbnail_path, 'user') }}" alt="" height="100"
                                width="100" class="mt-2 mb-4">
                            <div class="form-group">
                                <label class="form-control-file" for="gallery_path">Gallery Image</label>
                                <input name="gallery_path[]" type="file" multiple class="form-control-file"
                                    id="gallery_path">
                            </div>
                            @php
                                $galleryPaths = json_decode($shahid->gallery_path, true);
                            @endphp

                            @if (!empty($galleryPaths))
                                <div class="form-group">
                                    @foreach ($galleryPaths as $gallery_path)
                                        <img src="{{ get_file($gallery_path, 'user') }}" alt="" height="100" width="100" class="m-2">
                                    @endforeach
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="video_link">Video Link</label>
                                <input type="url" name="video_link" class="form-control" id="video_link"
                                    value="{{ $shahid->video_link }}">
                            </div>
                            <input type="submit" class="btn btn-primary" value="Update" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
