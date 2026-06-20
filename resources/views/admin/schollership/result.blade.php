@extends('admin.index')
@section('title')
ScholarShip Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show scholarShip</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.schollership.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="d-flex">
                            <h4 class="card-title">Scholarship <span class="text text-primary">{{ $scholarShip->title }}</span></h4>
                        </div>
                        <div class="d-flex">
                            <h4 class="card-title mr-2"> <a href="{{ route('scholarship.create') }}">Create</a></h4>
                            <h4 class="card-title"> <a href="{{ route('scholarship.edit', $scholarShip->slug) }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title mt-2">ScholarShip: <span class="text text-primary">{{ $scholarShip->title }}</span></h5>
                        <h5 class="card-title mt-2">Url: <span class="text text-primary">{{ $scholarShip->slug }}</span></h5>
                        <h5 class="card-title mt-2">Price: <span class="text text-primary">{{ $scholarShip->price }}</span></h5>
                        <h5 class="card-title mt-2">Enroll Limit: <span class="text text-primary">{{ $scholarShip->enroll_limit }}</span></h5>
                        <h5 class="card-title mt-2">Winner Limit: <span class="text text-primary">{{ $scholarShip->winner_limit }}</span></h5>
                        <h5 class="card-title mt-2">Date: <span class="text text-primary">{{ $scholarShip->date }}</span></h5>
                        <h5 class="card-title mt-2">Trime: <span class="text text-primary">{{ $scholarShip->time }}</span></h5>
                        <img height="250" width="250" class="mt-2" src="{{ $scholarShip->image }}">
                        <h5 class="card-title mt-2">Short Description: <span class="text text-primary">
                            @php
                                echo $scholarShip->short_description;
                            @endphp</span>
                        </h5>
                        <h5 class="card-title mt-2">Description: <span class="text text-primary">
                            @php
                                echo $scholarShip->description;
                            @endphp</span>
                        </h5>
                        <h5 class="card-title mt-2">Sponsor: <span class="text text-primary">{{ $scholarShip->sponsor }}</span></h5>
                        <img height="250" width="250" class="mt-2" src="{{ $scholarShip->sponsor_image }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
