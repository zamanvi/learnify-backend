@extends('layouts.admin')
@section('title')
    All lesson
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
                        <h3 class="card-title">Create lesson</h3>
                        <form method="POST" action="{{ route('lessons.store') }}">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $id }}">
                            <div class="form-group">
                                <label for="title">lesson Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="lesson title">
                            </div>
                            <div class="form-group">
                                <label for="pattern">Lesson Content Pattern</label>
                                <select name="pattern" id="pattern" class="form-control">
                                    <option value="">-- Select Pattern (Optional) --</option>
                                    <option value="standard">Standard (Meaning + Example)</option>
                                    <option value="exam">Exam (Meaning + Exam Type %)</option>
                                    <option value="medical">Medical (Meaning + Source + %)</option>
                                </select>
                                <small class="form-text text-muted">All words in this lesson will follow the same pattern.</small>
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
