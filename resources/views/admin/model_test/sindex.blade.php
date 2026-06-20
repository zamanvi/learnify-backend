@extends('admin.index')
@section('title')
    All Model Test
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Class</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.model_test.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Model Test<h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="/modeltest/{{ $modeltest->id }}/edit">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title">Class Name <span
                                class="text text-primary">{{ $modeltest->allclass->name }}</span></h5>
                        <h5 class="card-title">Model Test Name <span class="text text-primary">{{ $modeltest->name }}</span>
                        </h5>
                        <h5 class="card-title">Subject <span class="text text-primary">{{ $modeltest->subject }}</span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
