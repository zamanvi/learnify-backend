@extends('layouts.admin')
@section('title')
    Edit lesson
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
                        <h3 class="card-title">Edit lesson</h3>
                        <form method="POST" action="{{ route('lessons.update', $lesson->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="chapter_id" value="{{ $lesson->chapter_id }}">
                            <div class="form-group">
                                <label for="title">lesson Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    value="{{ $lesson->title }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Select Lesson Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option @selected($lesson->type == 'vocabulary') value="vocabulary">Vocabulary</option>
                                    <option @selected($lesson->type == 'grammar') value="grammar">Grammar</option>
                                    <option @selected($lesson->type == 'both') value="both">Both</option>
                                </select>
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
