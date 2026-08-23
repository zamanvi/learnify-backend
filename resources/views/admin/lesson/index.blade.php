@extends('layouts.admin')
@section('title')
    All lesson
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a>a></li>li>
            <li class="breadcrumb-item">Lesson</li>li>
            <li class="breadcrumb-item active" aria-current="page">All</li>li>
        </ul>ul>
    </nav>nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.lesson.item')
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Create lesson</h3>h3>
                        <form method="POST" action="{{ route('lessons.store') }}">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $id }}">
                            <div class="form-group">
                                <label for="title">lesson Title</label>label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="lesson title">
                            </div>div>
                            <div class="form-group">
                                <label>Word List Column Names <span class="text-muted">(optional)</span>span></label>label>
                                                            <small class="form-text text-muted d-block mb-2">
                                                                                                    Type a name for each column you want in this lesson's word list. Leave any of
                                                                                                    them blank to leave that column out entirely - both here and in the app.
                                                                                                    Leave all 4 blank to keep the default Word/Meaning/Synonyms/Antonyms columns.
                                                            </small>small>
                                                                <input type="text" name="col1_label" class="form-control mb-2" placeholder="Column 1 name (e.g. Word)">
                                                                <input type="text" name="col2_label" class="form-control mb-2" placeholder="Column 2 name (e.g. Meaning)">
                                                                <input type="text" name="col3_label" class="form-control mb-2" placeholder="Column 3 name (e.g. Synonyms)">
                                                                <input type="text" name="col4_label" class="form-control" placeholder="Column 4 name (e.g. Antonyms)">
                            </div>div>
                                                        <div class="form-group form-check">
                                                                                            <input type="checkbox" name="is_premium" id="is_premium" class="form-check-input" value="1">
                                                                                            <label for="is_premium" class="form-check-label">Premium (unlock costs 50 Lipto)</label>label>
                                                        </div>div>
                                                        <input type="submit" class="btn btn-primary" value="Submit" />
                                                        <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>form>
                    </div>div>
                </div>div>
            </div>div>
        </div>div>
    </div>div>
@endsection

                                                            </label>
