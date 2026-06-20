@extends('admin.index')
@section('title')
    Edit Country
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Country</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.address.country.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit Country {{ $country->name }}</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('country.update', $country->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <input required type="text" name="name" class="form-control" id="name"
                                    value="{{ $country->name }}">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Submit" />
                                <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
