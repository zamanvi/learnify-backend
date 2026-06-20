@extends('admin.index')
@section('title')
    All Cities
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('country.index') }}">{{ $division->country->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('division.index', $division->country_id) }}">{{ $division->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Cities</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.address.city.component')
        </div>
    </div>
@endsection
