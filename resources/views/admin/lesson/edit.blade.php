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
                                <label>Word List Column Names <span class="text-muted">(optional)</span></label>
                                <small class="form-text text-muted d-block mb-2">
                                    Type a name for each column you want in this lesson's word list. Leave any of
                                    them blank to leave that column out entirely - both here and in the app.
                                    Leave all 4 blank to keep the default Word/Meaning/Synonyms/Antonyms columns.
                                </small>
                                <input type="text" name="col1_label" class="form-control mb-2" placeholder="Column 1 name (e.g. Word)" value="{{ $lesson->col1_label }}">
                                <input type="text" name="col2_label" class="form-control mb-2" placeholder="Column 2 name (e.g. Meaning)" value="{{ $lesson->col2_label }}">
                                <input type="text" name="col3_label" class="form-control mb-2" placeholder="Column 3 name (e.g. Synonyms)" value="{{ $lesson->col3_label }}">
                                <input type="text" name="col4_label" class="form-control" placeholder="Column 4 name (e.g. Antonyms)" value="{{ $lesson->col4_label }}">
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
