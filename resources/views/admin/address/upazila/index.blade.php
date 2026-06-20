@extends('admin.index')
@section('title')
    All Upazila
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('country.index') }}">{{ $city->division->country->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('division.index', $city->division->country_id) }}">{{ $city->division->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('city.index', $city->division_id) }}">{{ $city->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Upazila</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.address.upazila.component')
        </div>
    </div>
@endsection
