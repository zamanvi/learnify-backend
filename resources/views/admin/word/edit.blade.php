@extends('layouts.admin')
@section('title')
    Edit Word
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Word</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.word.item')
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Edit Word</h3>
                        @php $cols = $lesson->column_labels; @endphp
                        @if($lesson->col1_label || $lesson->col2_label || $lesson->col3_label || $lesson->col4_label)
                        <p class="text-muted small">This lesson uses custom column names set on the lesson itself (Edit Lesson).</p>
                        @else
                        <p class="text-muted small">
                            For <strong>Verb</strong>: {{ $cols['word']['label'] }} = V1, {{ $cols['synonyms']['label'] }} = V2, {{ $cols['antonyms']['label'] }} = V3<br>
                            For <strong>Vocabulary</strong>: {{ $cols['word']['label'] }}, {{ $cols['meaning']['label'] }}, {{ $cols['synonyms']['label'] }}, {{ $cols['antonyms']['label'] }}
                        </p>
                        @endif
                        <form method="POST" action="{{ route('words.update', $word->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @if($cols['word']['show'])
                            <div class="form-group">
                                <label for="word">{{ $cols['word']['label'] }}</label>
                                <input required type="text" name="word" class="form-control" id="word"
                                    value="{{ $word->word }}" placeholder="{{ $cols['word']['placeholder'] }}">
                            </div>
                            @endif
                            @if($cols['meaning']['show'])
                            <div class="form-group">
                                <label for="meaning">{{ $cols['meaning']['label'] }}</label>
                                <input type="text" name="meaning" class="form-control" id="meaning"
                                    value="{{ $word->meaning }}" placeholder="{{ $cols['meaning']['placeholder'] }}">
                            </div>
                            @endif
                            @if($cols['synonyms']['show'])
                            <div class="form-group">
                                <label for="synonyms">{{ $cols['synonyms']['label'] }}</label>
                                <input type="text" name="synonyms" class="form-control" id="synonyms"
                                    value="{{ $word->synonyms }}" placeholder="{{ $cols['synonyms']['placeholder'] }}">
                            </div>
                            @endif
                            @if($cols['antonyms']['show'])
                            <div class="form-group">
                                <label for="antonyms">{{ $cols['antonyms']['label'] }}</label>
                                <input type="text" name="antonyms" class="form-control" id="antonyms"
                                    value="{{ $word->antonyms }}" placeholder="{{ $cols['antonyms']['placeholder'] }}">
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="image">Picture (for the Picture quiz round)</label>
                                @if($word->image)
                                    <div class="mb-2"><img src="{{ $word->image_url }}" alt="" style="max-height:80px;border-radius:6px;"></div>
                                @endif
                                <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                <small class="text-muted">Optional. Leave empty to keep the current picture.</small>
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
