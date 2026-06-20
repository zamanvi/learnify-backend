@extends('admin.index')
@section('title')
    blog Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit blog</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.blog.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit blog</h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="{{ route('blog.create') }}">Create</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('blog.update', $blog->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Blog Name</label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ $blog->name }}">
                            </div>
                            <div class="form-group">
                                <label for="slug">Blog Url</label>
                                <input type="text" name="slug" class="form-control" id="slug" value="{{ $blog->slug }}">
                            </div>
                            <div class="form-group">
                                <div class="iq-card-body m-0 p-0">
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="short_description-tab" data-toggle="pill"
                                                href="#short_description" role="tab" aria-controls="short_description"
                                                aria-selected="true">Post Short Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="keyword-tab" data-toggle="pill"
                                                href="#keyword" role="tab" aria-controls="keyword"
                                                aria-selected="false">Keyword</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent-2">
                                        <div class="tab-pane fade show active" id="short_description" role="tabpanel"
                                            aria-labelledby="short_description-tab">
                                            <input type="text" name="short_description" class="form-control"
                                                id="short_description" value="{{ $blog->short_description }}">
                                        </div>
                                        <div class="tab-pane fade" id="keyword" role="tabpanel"
                                            aria-labelledby="keyword-tab">
                                            <input type="text" name="keyword" class="form-control"
                                                id="keyword" value="{{ $blog->keyword }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">blog Description</label>
                                <textarea class="form-control" name="description" id="editor1" rows="5">
                                    {{ $blog->description }}
                                </textarea>
                            </div>
                            <img height="250" width="250" src="{{ get_file($blog->image_path, 'blog') }}">
                            <div class="form-group">
                                <label class="form-control-file" for="customFile">blog Feature Image</label>
                                <input name="image_path" type="file" class="form-control-file" id="customFile">
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
