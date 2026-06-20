@extends('admin.index')
@section('title')
    Shahid Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show Shahid</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.shahid.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="d-flex">
                            <h4 class="card-title">Shahid <span class="text text-primary">{{ $shahid->name }}</span></h4>
                        </div>
                        <div class="d-flex">
                            <h4 class="card-title mr-2"> <a href="{{ route('shahid.create') }}">Create</a></h4>
                            <h4 class="card-title"> <a href="{{ route('shahid.edit', $shahid->slug) }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">

                        <h5 class="card-title mt-2">Shahid Name: <span class="text text-primary">{{ $shahid->name }}</span>
                        </h5>
                        <h5 class="card-title mt-2">Shahid Address: <span
                                class="text text-primary">{{ $shahid->address }}</span></h5>
                        <h5 class="card-title mt-2">Shahid Url: <span class="text text-primary">{{ $shahid->slug }}</span>
                        </h5>
                        <img height="250" width="250" src="{{ get_file($shahid->thumbnail_path, 'shahid') }}">

                        @php
                            $galleryPaths = json_decode($shahid->gallery_path, true);
                        @endphp

                        @if (!empty($galleryPaths))
                            <div class="">
                                @foreach ($galleryPaths as $gallery_path)
                                    <img src="{{ get_file($gallery_path, 'user') }}" alt="" height="100"
                                        width="100" class="m-2">
                                @endforeach
                            </div>
                        @endif

                        <h5 class="card-title">shahid Description: <span class="text text-primary">
                                @php
                                    echo $shahid->description;
                                @endphp</span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
