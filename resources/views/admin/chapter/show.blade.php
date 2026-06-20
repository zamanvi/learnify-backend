@extends('layouts.admin')
@section('title')
    Show Chapter
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Chapter</li>
            <li class="breadcrumb-item active" aria-current="page">Show</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.chapter.item')
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Show chapter</h3>
                        <form>
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input readonly type="text" name="title" class="form-control" value="{{ $chapter->title }}" id="title">
                            </div>
                            <div class="form-group">
                                <label for="type">Chapter Type</label>
                                <select disabled @readonly(true) name="type" id="type" required class="form-control">
                                    <option @selected($chapter->type == 'vocabulary') value="vocabulary">Vocabulary</option>
                                    <option @selected($chapter->type == 'grammar') value="grammar">Grammar</option>
                                    <option @selected($chapter->type == 'both') value="both">Both</
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image_path">Chapter Image</label>
                                <div class="custom-file">
                                    <input disabled readonly type="file" class="custom-file-input" id="image_path">
                                    <label class="custom-file-label" for="image_path">Choose file</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
