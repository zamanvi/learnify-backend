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
                        <p class="text-muted small">
                            For <strong>Verb</strong>: Word = V1, Synonyms = V2, Antonyms = V3<br>
                            For <strong>Vocabulary</strong>: Word, Meaning, Synonyms, Antonyms
                        </p>
                        <form method="POST" action="{{ route('words.update', $word->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="word">Word / V1</label>
                                <input required type="text" name="word" class="form-control" id="word"
                                    value="{{ $word->word }}">
                            </div>
                            <div class="form-group">
                                <label for="meaning">Meaning</label>
                                <input type="text" name="meaning" class="form-control" id="meaning"
                                    value="{{ $word->meaning }}">
                            </div>
                            <div class="form-group">
                                <label for="synonyms">Synonyms / V2</label>
                                <input type="text" name="synonyms" class="form-control" id="synonyms"
                                    value="{{ $word->synonyms }}">
                            </div>
                            <div class="form-group">
                                <label for="antonyms">Antonyms / V3</label>
                                <input type="text" name="antonyms" class="form-control" id="antonyms"
                                    value="{{ $word->antonyms }}">
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
