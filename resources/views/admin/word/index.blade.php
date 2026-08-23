@extends('layouts.admin')
@section('title')
    All Word
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
                        <h3 class="card-title">Create word</h3>
                        <form method="POST" action="{{ route('words.store') }}">
                            @csrf
                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                            @php $cols = $lesson->column_labels; @endphp
                            @if($cols['word']['show'])
                            <div class="form-group">
                                <label for="word">{{ $cols['word']['label'] }}</label>
                                <input required type="text" name="word" class="form-control" id="word"
                                    placeholder="{{ $cols['word']['placeholder'] }}">
                            </div>
                            @endif
                            @if($cols['meaning']['show'])
                            <div class="form-group">
                                <label for="meaning">{{ $cols['meaning']['label'] }}</label>
                                <input type="text" name="meaning" class="form-control" id="meaning"
                                    placeholder="{{ $cols['meaning']['placeholder'] }}">
                            </div>
                            @endif
                            @if($cols['synonyms']['show'])
                            <div class="form-group">
                                <label for="synonyms">{{ $cols['synonyms']['label'] }}</label>
                                <input type="text" name="synonyms" class="form-control" id="synonyms"
                                    placeholder="{{ $cols['synonyms']['placeholder'] }}">
                            </div>
                            @endif
                            @if($cols['antonyms']['show'])
                            <div class="form-group">
                                <label for="antonyms">{{ $cols['antonyms']['label'] }}</label>
                                <input type="text" name="antonyms" class="form-control" id="antonyms"
                                    placeholder="{{ $cols['antonyms']['placeholder'] }}">
                            </div>
                            @endif
                           <div class="form-group">
                                <label for="type">Word Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option value="">-- Select a Word Type --</option>
                                    <option value="vocabulary">Vocabulary</option>
                                    <option value="verb">Verb</option>
                                </select>
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
