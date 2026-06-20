@extends('layouts.admin')
@section('title')
    All chapter
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Chapter</li>
            <li class="breadcrumb-item active" aria-current="page">All</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.chapter.item')
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Create chapter</h3>
                        <form method="POST" action="{{ route('chapters.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="chapter title">
                            </div>
                           <div class="form-group">
                                <label for="type">Chapter Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option value="">-- Select a Chapter Type --</option>
                                    <option value="vocabulary">Vocabulary</option>
                                    <option value="grammar">Grammar</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image_path">Chapter Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image_path">
                                    <label class="custom-file-label" for="image_path">Choose file</label>
                                </div>
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
