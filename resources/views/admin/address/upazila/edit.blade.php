@extends('admin.index')
@section('title')
    Edit City
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('country.index') }}">{{ $city->division->country->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('division.index', $city->division->country_id) }}">{{ $city->division->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('city.index', $city->division_id) }}">{{ $city->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Upazila</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.address.upazila.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Update Upazila</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('upazila.update', $upazila->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <input required type="text" name="name" class="form-control"
                                    value="{{ $upazila->name }}">
                            </div>
                            <input type="submit" class="btn btn-primary" value="Update" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
