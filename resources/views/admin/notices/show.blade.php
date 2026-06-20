@extends('admin.index')
@section('title')
    Notice Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show notice</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.notices.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Notice <span class="text text-primary">{{ $notice->title }}</span></h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="{{ route('notices.edit', $notice->id) }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title mt-2">Notice Title: <span class="text text-primary">{{ $notice->title }}</span></h5>
                        <h5 class="card-title mt-2">Notice App Type: <span class="text text-primary">{{ $notice->app }}</span></h5>
                        <h5 class="card-title">Notice Body: <span class="text text-primary">
                            @php
                                echo $notice->body;
                            @endphp</span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
