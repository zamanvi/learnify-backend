@extends('layouts.admin')
@section('title')
    Show lesson
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">lesson</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.lesson.item')
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Show lesson</h3>
                        <form method="POST" action="{{ route('lessons.update', $lesson->id) }}">
                            <input type="hidden" name="chapter_id" value="{{ $lesson->chapter_id }}">
                            <div class="form-group">
                                <label for="title">lesson Title</label>
                                <input readonly type="text" name="title" class="form-control" id="title"
                                    value="{{ $lesson->title }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Lesson type</label>
                                <input readonly type="text" class="form-control" id="type"
                                    value="{{ $lesson->type }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
