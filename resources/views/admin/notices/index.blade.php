@extends('admin.index')
@section('title')
    Notice Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notices</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.notices.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Create New Notice</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('notices.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="title">Notice title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="Notice title">
                            </div>
                            <div class="form-group">
                                <label for="app">App Type</label>
                                <select required name="app" class="form-control" id="app">
                                    <option value="grammar">Grammar</option>
                                    <option value="paragraph">Paragraph</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="body">Notice Body</label>
                                <input required type="text" name="body" class="form-control" id="body"
                                    placeholder="Notice Body">
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
