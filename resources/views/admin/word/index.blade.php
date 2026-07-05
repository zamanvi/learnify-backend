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
                            <div class="form-group">
                                <label for="word">{{ $lesson->type === 'verb' ? 'Verb 1' : 'Word' }}</label>
                                <input required type="text" name="word" class="form-control" id="word"
                                    placeholder="{{ $lesson->type === 'verb' ? 'Verb 1' : 'Word' }}">
                            </div>
                            <div class="form-group">
                                <label for="meaning">{{ $lesson->type === 'verb' ? 'Verb ' : 'Word ' }} meaning</label>
                                <input type="text" name="meaning" class="form-control" id="meaning"
                                    placeholder="{{ $lesson->type === 'verb' ? 'Verb ' : 'Word ' }} meaning">
                            </div>
                            <div class="form-group">
                                <label for="synonyms">{{ $lesson->type === 'verb' ? 'Verb 2' : 'Word synonyms' }}</label>
                                <input type="text" name="synonyms" class="form-control" id="synonyms"
                                    placeholder="{{ $lesson->type === 'verb' ? 'Verb 2' : 'Word synonyms' }} ">
                            </div>
                            <div class="form-group">
                                <label for="antonyms">{{ $lesson->type === 'verb' ? 'Verb 3' : 'Word antonyms' }}</label>
                                <input type="text" name="antonyms" class="form-control" id="antonyms"
                                    placeholder="{{ $lesson->type === 'verb' ? 'Verb 3' : 'Word antonyms' }}">
                            </div>
                           <div class="form-group">
                                <label for="type">Word Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option value="">-- Select a Word Type --</option>
                                    <option value="vocabulary">Vocabulary</option>
                                    <option value="grammar">Grammar</option>
                                    <option value="both">Both</option>
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
