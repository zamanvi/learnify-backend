@extends('admin.index')
@section('title')
    {{ Auth::user()->name }}
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">User App Version Control</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">User App Version</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('app-version.store') }}">
                            @csrf
                            <div class="form-group mt-2">
                                <label for="app_version">App Version</label>
                                <input name="app_version" type="text" value="{{ $app_version ?? 'Enter App Version' }}" class="form-control" id="app_version">
                            </div>
                            <div class="form-group mt-2">
                                <label for="app_version_text">App Version Update Text</label>
                                <input name="app_version_text" type="text" value="{{ $app_version_text ?? 'Enter App Version Update Text' }}" class="form-control" id="app_version">
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
