@extends('layouts.admin')
@section('title')
    Show word
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">word</li>
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
                        <h3 class="card-title">Show Word</h3>
                        <form method="POST" action="{{ route('words.update', $word->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="chapter_id" value="{{ $word->chapter_id }}">
                            <div class="form-group">
                                <label for="word">{{ $lesson->type === 'verb' ? 'Verb 1' : 'Word' }}</label>
                                <input readonly type="text" class="form-control" id="word"
                                    value="{{ $word->word }}">
                            </div>
                            <div class="form-group">
                                <label for="meaning">{{ $lesson->type === 'verb' ? 'Verb ' : 'Word ' }} meaning</label>
                                <input readonly type="text" class="form-control" id="meaning"
                                    value="{{ $word->meaning }}">
                            </div>
                            <div class="form-group">
                                <label for="synonyms">{{ $lesson->type === 'verb' ? 'Verb 2' : 'Word synonyms' }} </label>
                                <input readonly type="text" class="form-control" id="synonyms"
                                    value="{{ $word->synonyms }}">
                            </div>
                            <div class="form-group">
                                <label for="antonyms">{{ $lesson->type === 'verb' ? 'Verb 3' : 'Word antonyms' }} </label>
                                <input readonly type="text" class="form-control" id="antonyms"
                                    value="{{ $word->antonyms }}">
                            </div>
                             <div class="form-group">
                                <label for="type">Word Type</label>
                                <select @disabled(true) name="type" id="type" required class="form-control">
                                    <option @selected($word->type === 'vocabulary') value="vocabulary">Vocabulary</option>
                                    <option @selected($word->type === 'grammar') value="grammar">Grammar</option>
                                    <option @selected($word->type === 'both') value="both">Both</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
