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
                                <label for="pattern">Lesson Content Pattern</label>
                                <select name="pattern" id="pattern" class="form-control">
                                    <option value="">-- Select Pattern (Optional) --</option>
                                    <option value="standard" @selected($lesson->pattern === 'standard')>Standard (Meaning + Example)</option>
                                    <option value="exam" @selected($lesson->pattern === 'exam')>Exam (Meaning + Exam Type %)</option>
                                    <option value="medical" @selected($lesson->pattern === 'medical')>Medical (Meaning + Source + %)</option>
                                </select>
                                <small class="form-text text-muted">All words in this lesson will follow the same pattern.</small>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" name="is_premium" id="is_premium" class="form-check-input" value="1" @checked($lesson->is_premium)>
                                <label for="is_premium" class="form-check-label">Premium (unlock costs 50 Lipto)</label>
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
