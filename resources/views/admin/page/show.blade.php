@extends('admin.index')
@section('title')
    page Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show page</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.page.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Page <span class="text text-primary">{{ $page->title }}</span></h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="{{ route('page.edit', $page->id) }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title mt-2">Page Title: <span class="text text-primary">{{ $page->title }}</span></h5>
                        <h5 class="card-title mt-2">Page Type: <span class="text text-primary">{{ $page->type }}</span></h5>
                        <h5 class="card-title mt-2">Page Slug: <span class="text text-primary">{{ $page->slug }}</span></h5>
                        <h5 class="card-title">page Description: <span class="text text-primary">
                            @php
                                echo $page->description;
                            @endphp</span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
