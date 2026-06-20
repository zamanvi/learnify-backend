@extends('admin.index')
@section('title')
    page Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">page</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.page.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Create New page</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('page.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="title">Page title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="Page title">
                            </div>
                            <div class="form-group">
                                <label for="type">Page Type</label>
                                <input required type="text" name="type" class="form-control" id="type"
                                    placeholder="Page Type">
                            </div>
                            <div class="form-group">
                                <label for="editor1">Page Description</label>
                                <textarea class="form-control" name="description" id="editor1" rows="5"></textarea>
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
