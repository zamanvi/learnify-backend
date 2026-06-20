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
                        <h3 class="card-title">Edit word</h3>
                        <form method="POST" action="{{ route('words.update', $word->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="word">{{ $lesson->chapter_type === 'verb' ? 'Verb 1' : 'Word' }}</label>
                                <input required type="text" name="word" class="form-control" id="word"
                                    value="{{ $word->word }}">
                            </div>
                            <div class="form-group">
                                <label for="meaning">{{ $lesson->chapter_type === 'verb' ? 'Verb ' : 'Word ' }} meaning</label>
                                <input type="text" name="meaning" class="form-control" id="meaning"
                                value="{{ $word->meaning }}">
                            </div>
                            <div class="form-group">
                                <label for="synonyms">{{ $lesson->chapter_type === 'verb' ? 'Verb 2' : 'Word synonyms' }} </label>
                                <input type="text" name="synonyms" class="form-control" id="synonyms"
                                value="{{ $word->synonyms }}">
                            </div>
                            <div class="form-group">
                                <label for="antonyms">{{ $lesson->chapter_type === 'verb' ? 'Verb 1' : 'Word antonyms' }} </label>
                                <input type="text" name="antonyms" class="form-control" id="antonyms"
                                value="{{ $word->antonyms }}">
                            </div>
                           <div class="form-group">
                                <label for="type">Word Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option @selected($word->type === 'vocabulary') value="vocabulary">Vocabulary</option>
                                    <option @selected($word->type === 'grammar') value="grammar">Grammar</option>
                                    <option @selected($word->type === 'both') value="both">Both</option>
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
