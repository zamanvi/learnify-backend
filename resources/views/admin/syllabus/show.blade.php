@extends('admin.index')
@section('title')
    Events
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Syllabus - {{ $syllabus->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.syllabus.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Syllabus <span class="text text-primary">{{ $syllabus->name }}</span>
                            </h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="/syllabus/edit/{{ $syllabus->id }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title">Event Name: <span
                                class="text text-primary">{{ $syllabus->events->name }}</span></h5>
                        <h5 class="card-title">Syllabus Name: <span class="text text-primary">{{ $syllabus->name }}</span>
                        </h5>
                        <h5 class="card-title">Syllabus Description:
                        </h5>
                        <span>
                            @php
                                echo $syllabus->description;
                            @endphp
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
