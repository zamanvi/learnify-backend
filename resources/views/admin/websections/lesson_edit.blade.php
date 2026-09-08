@extends('layouts.admin')
@section('title')
    Edit Lesson
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('websections.index') }}">Website Sections</a></li>
            <li class="breadcrumb-item"><a href="{{ route('websections.lessons', $chapter->slug) }}">{{ $chapter->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Lesson</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Edit Lesson</h3>
                        <form method="POST" action="{{ route('websections.lesson.update', $lesson->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Chapter</label>
                                <input readonly class="form-control" value="{{ $chapter->title }}">
                            </div>
                            <div class="form-group">
                                <label for="title">Lesson Title</label>
                                <input required type="text" name="title" class="form-control" id="title" value="{{ $lesson->title }}">
                            </div>
                            <div class="form-group">
                                <label for="slug">Lesson Url</label>
                                <input type="text" name="slug" class="form-control" id="slug" value="{{ $lesson->slug }}">
                            </div>
                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea name="content" class="form-control" id="content" rows="6">{{ $lesson->content }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="short_details">Short Details (SEO meta description)</label>
                                <input type="text" name="short_details" class="form-control" id="short_details" value="{{ $lesson->short_details }}">
                            </div>
                            <div class="form-group">
                                <label for="link">Video Link (YouTube, optional)</label>
                                <input type="text" name="link" class="form-control" id="link" value="{{ $lesson->link }}">
                            </div>
                            <div class="form-group">
                                <label for="keyword">Keywords (SEO, optional)</label>
                                <input type="text" name="keyword" class="form-control" id="keyword" value="{{ $lesson->keyword }}">
                            </div>
                            <div class="form-group">
                                <label for="order">Order</label>
                                <input type="number" name="order" class="form-control" id="order" value="{{ $lesson->order }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" @checked($lesson->is_active)>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Update">
                            <a href="{{ route('websections.lessons', $chapter->slug) }}" class="btn iq-bg-danger">Cancel</a>
                        </form>

                        <form method="POST" action="{{ route('websections.lesson.delete', $lesson->id) }}" class="mt-3"
                              onsubmit="return confirm('Delete this lesson? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete Lesson</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
