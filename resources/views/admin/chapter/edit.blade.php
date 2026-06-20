@extends('layouts.admin')
@section('title')
    Edit chapter
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Chapter</li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                        <h3 class="card-title">Edit chapter</h3>
                        <form method="POST" action="{{ route('chapters.update', $chapter->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" value="{{ $chapter->title }}" id="title">
                            </div>
                            <div class="form-group">
                                <label for="plan_id">Chapter Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option @selected($chapter->type == 'vocabulary') value="vocabulary">Vocabulary</option>
                                    <option @selected($chapter->type == 'grammar') value="grammar">Grammar</option>
                                    <option @selected($chapter->type == 'both') value="both">Both</option>
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
