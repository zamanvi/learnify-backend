@extends('layouts.admin')
@section('title')
    Create Word
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
                        <h3 class="card-title">Create Word</h3>
                        <p class="text-muted small">
                            For <strong>Verb</strong>: Word = V1, Synonyms = V2, Antonyms = V3<br>
                            For <strong>Vocabulary</strong>: Word, Meaning, Synonyms, Antonyms
                        </p>
                        <form method="POST" action="{{ route('words.store') }}">
                            @csrf
                            <input hidden name="lesson_id" value="{{ $lesson->id }}" />
                            <div class="form-group">
                                <label for="word">Word / V1</label>
                                <input required type="text" name="word" class="form-control" id="word"
                                    placeholder="Enter word or V1 (base form)">
                            </div>
                            <div class="form-group">
                                <label for="meaning">Meaning</label>
                                <input type="text" name="meaning" class="form-control" id="meaning"
                                    placeholder="Enter meaning (Bengali or English)">
                            </div>
                            <div class="form-group">
                                <label for="synonyms">Synonyms / V2</label>
                                <input type="text" name="synonyms" class="form-control" id="synonyms"
                                    placeholder="Synonyms (vocabulary) or V2 past tense (verb)">
                            </div>
                            <div class="form-group">
                                <label for="antonyms">Antonyms / V3</label>
                                <input type="text" name="antonyms" class="form-control" id="antonyms"
                                    placeholder="Antonyms (vocabulary) or V3 past participle (verb)">
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
