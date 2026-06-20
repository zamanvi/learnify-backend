@extends('admin.index')
@section('title')
    All Divitions
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('country.index') }}">{{ $country->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Divitions</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.address.division.component')
        </div>
    </div>
@endsection
