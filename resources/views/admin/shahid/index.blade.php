@extends('admin.index')
@section('title')
Shahid Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">shahid</li>
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
                            <h4 class="card-title">Create New shahid</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('shahid.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="required">Shahid Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    placeholder="shahid Name">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea required type="text" name="address" class="form-control" id="address"
                                    placeholder="address" rows="3"> </textarea>
                            </div>
                            <div class="form-group">
                                <label for="editor1">Description</label>
                                <textarea required class="form-control" name="description" id="editor1" rows="10"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-control-file" for="thumbnail_path">Thumbnail Image</label>
                                <input required name="thumbnail_path" type="file" class="form-control-file" id="thumbnail_path">
                            </div>
                            <div class="form-group">
                                <label class="form-control-file" for="gallery_path">Gallery Image</label>
                                <input name="gallery_path[]" type="file" multiple class="form-control-file" id="gallery_path">
                            </div>
                            <div class="form-group">
                                <label for="video_link">Video Link</label>
                                <input type="url" name="video_link" class="form-control" id="video_link"
                                    placeholder="video Link">
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
