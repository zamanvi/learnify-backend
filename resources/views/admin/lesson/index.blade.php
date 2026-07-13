@extends('layouts.admin')
@section('title')
    All lesson
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Lesson</li>
            <li class="breadcrumb-item active" aria-current="page">All</li>
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
                                <label for="type">Select Lesson Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option value="">-- Select a Type --</option>
                                    <option value="vocabulary">Vocabulary</option>
                                    <option value="grammar">Grammar</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" name="is_premium" id="is_premium" class="form-check-input" value="1">
                                <label for="is_premium" class="form-check-label">Premium (unlock costs 50 Lipto)</label>
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
