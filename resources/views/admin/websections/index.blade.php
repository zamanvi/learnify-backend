@extends('layouts.admin')
@section('title')
    Website Sections
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Website Sections</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Website Sections</h3>
                        <p class="text-muted">masterenglishbook.com content, grouped by audience. Pick a section to
                            manage its chapters/lessons. This is separate from the app's Book/Vocabulary/Wizard
                            content above.</p>

                        <div class="row mt-3">
                            @foreach ($sections as $section)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('websections.chapters', $section->slug) }}" class="text-decoration-none">
                                        <div class="card {{ $section->slug == 'bangladesh' ? 'iq-bg-success' : 'iq-bg-primary' }}" style="min-height:120px;">
                                            <div class="card-body text-center">
                                                <i class="ri-earth-line" style="font-size:28px;"></i>
                                                <h5 class="mt-2 mb-0">{{ $section->name }}</h5>
                                                @unless ($section->is_active)
                                                    <span class="badge badge-secondary mt-1">Inactive</span>
                                                @endunless
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
