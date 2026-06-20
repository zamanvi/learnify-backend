@extends('admin.index')
@section('title')
    Contest
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contest - {{ $contest->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.contest.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Contest <span class="text text-primary">{{ $contest->name }}</span></h4>
                        </div>
                        <div class="iq-header-title">
                            <h4 class="card-title"> <a href="{{ route('contest.edit', $contest->slug) }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title">Price <span class="text text-primary">{{ $contest->price }}</span></h5>
                        <h5 class="card-title">Exam Duration <span class="text text-primary">{{ $contest->duration }}</span>
                        </h5>
                        <h5 class="card-title">Exam Date <span class="text text-primary">{{ $contest->date }}</span></h5>
                        <h5 class="card-title">Exam Time <span class="text text-primary">{{ $contest->time }}</span></h5>
                        <h5 class="card-title">Exam Status <span
                                class="text text-primary">{{ $contest->status ? 'Active' : 'Inactive' }}</span></h5>
                        <img height="250" width="250" src="{{ get_file($contest->image_path, 'contest') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
