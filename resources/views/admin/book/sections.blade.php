@extends('layouts.admin')
@section('title')
    Sections
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('book.index') }}">Book</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sections</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ $book->title }} — Sections</h3>
                        <p class="text-muted">Pick a section to manage its chapters/lessons. Vocabulary and Wizard
                            are managed in their own separate screens (they aren't part of this book's chapters).</p>

                        <div class="row mt-3">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('chapters.index') }}" class="text-decoration-none">
                                    <div class="card iq-bg-primary" style="min-height:120px;">
                                        <div class="card-body text-center">
                                            <i class="ri-book-open-line" style="font-size:28px;"></i>
                                            <h5 class="mt-2 mb-0">Vocabulary</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('chapter.index', $book->slug) }}?type=grammar" class="text-decoration-none">
                                    <div class="card iq-bg-danger" style="min-height:120px;">
                                        <div class="card-body text-center">
                                            <i class="ri-quill-pen-line" style="font-size:28px;"></i>
                                            <h5 class="mt-2 mb-0">Grammar</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('wizard.chapter.index') }}" class="text-decoration-none">
                                    <div class="card iq-bg-warning" style="min-height:120px;">
                                        <div class="card-body text-center">
                                            <i class="ri-magic-line" style="font-size:28px;"></i>
                                            <h5 class="mt-2 mb-0">Wizard</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('chapter.index', $book->slug) }}?type=writing_reading" class="text-decoration-none">
                                    <div class="card iq-bg-success" style="min-height:120px;">
                                        <div class="card-body text-center">
                                            <i class="ri-edit-2-line" style="font-size:28px;"></i>
                                            <h5 class="mt-2 mb-0">Reading & Writing</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('chapter.index', $book->slug) }}" class="btn btn-sm btn-outline-secondary">
                            View all chapters (unfiltered)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
