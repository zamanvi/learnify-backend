@extends('layouts.admin')
@section('title')
    Edit Chapter
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('websections.index') }}">Website Sections</a></li>
            <li class="breadcrumb-item"><a href="{{ route('websections.chapters', $section->slug) }}">{{ $section->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Chapter</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Edit Chapter</h3>
                        <form method="POST" action="{{ route('websections.chapter.update', $chapter->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Section</label>
                                <input readonly class="form-control" value="{{ $section->name }}">
                            </div>
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" id="title" value="{{ $chapter->title }}">
                            </div>
                            <div class="form-group">
                                <label for="slug">Chapter Url</label>
                                <input type="text" name="slug" class="form-control" id="slug" value="{{ $chapter->slug }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" rows="3">{{ $chapter->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="order">Order</label>
                                <input type="number" name="order" class="form-control" id="order" value="{{ $chapter->order }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" @checked($chapter->is_active)>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Update">
                            <a href="{{ route('websections.chapters', $section->slug) }}" class="btn iq-bg-danger">Cancel</a>
                        </form>

                        <form method="POST" action="{{ route('websections.chapter.delete', $chapter->id) }}" class="mt-3"
                              onsubmit="return confirm('Delete this chapter and all its lessons? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete Chapter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
