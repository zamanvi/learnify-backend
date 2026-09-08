@extends('layouts.admin')
@section('title')
    {{ $chapter->title }} - Lessons
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('websections.index') }}">Website Sections</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $chapter->title }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ $chapter->title }} — Lessons</h3>
                        <table class="table table-striped table-bordered mt-1">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Active</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lessons as $lesson)
                                    <tr>
                                        <td>{{ $lesson->order }}</td>
                                        <td>{{ $lesson->title }}</td>
                                        <td>
                                            @if ($lesson->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('websections.lesson.edit', $lesson->slug) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No lessons yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $lessons->links() }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Add Lesson</h3>
                        <form method="POST" action="{{ route('websections.lesson.store') }}">
                            @csrf
                            <input type="hidden" name="web_chapter_id" value="{{ $chapter->id }}">
                            <div class="form-group">
                                <label>Chapter</label>
                                <input readonly class="form-control" value="{{ $chapter->title }}">
                            </div>
                            <div class="form-group">
                                <label for="title">Lesson Title</label>
                                <input required type="text" name="title" class="form-control" id="title" placeholder="Lesson title">
                            </div>
                            <div class="form-group">
                                <label for="slug">Lesson Url (optional)</label>
                                <input type="text" name="slug" class="form-control" id="slug" placeholder="auto-generated if left blank">
                            </div>
                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea name="content" class="form-control" id="content" rows="6"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="short_details">Short Details (SEO meta description)</label>
                                <input type="text" name="short_details" class="form-control" id="short_details">
                            </div>
                            <div class="form-group">
                                <label for="link">Video Link (YouTube, optional)</label>
                                <input type="text" name="link" class="form-control" id="link">
                            </div>
                            <div class="form-group">
                                <label for="keyword">Keywords (SEO, optional)</label>
                                <input type="text" name="keyword" class="form-control" id="keyword">
                            </div>
                            <div class="form-group">
                                <label for="order">Order</label>
                                <input type="number" name="order" class="form-control" id="order" value="0">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn iq-bg-danger" value="Cancel">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
