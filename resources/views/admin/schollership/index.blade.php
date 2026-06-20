@extends('admin.index')
@section('title')
ScholarShip Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Scholar Ship</li>
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
                        <div class="iq-header-title">
                            <h4 class="card-title">Create New Scholarship</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('scholarship.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="title">Scholarship title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="Scholarship title">
                            </div>
                            <div class="form-group">
                                <label for="slug">Scholarship Url</label>
                                <input type="text" name="slug" class="form-control" id="slug"
                                    placeholder="Scholarship Url">
                            </div>
                            <div class="form-group">
                                <label for="price">Scholarship Price</label>
                                <input type="number" required name="price" class="form-control" id="price"
                                    placeholder="Scholarship price">
                            </div>
                            <div class="form-group">
                                <label for="enroll_limit">Scholarship Enroll Limit</label>
                                <input type="number" required name="enroll_limit" class="form-control" id="enroll_limit"
                                    placeholder="Scholarship Enroll Limit">
                            </div>
                            <div class="form-group">
                                <label for="winner_limit">Scholarship Winner Limit</label>
                                <input type="number" required name="winner_limit" class="form-control" id="winner_limit"
                                    placeholder="Scholarship Winner Limit">
                            </div>
                            <div class="form-group">
                                <label for="date">Select Date</label>
                                <input type="date" required name="date" class="form-control" id="date">
                            </div>
                            <div class="form-group">
                                <label for="time">Set Time</label>
                                <input type="time" required name="time" class="form-control" id="time">
                            </div>
                            <div class="form-group">
                                <label for="short_description">Scholarship Short Description</label>
                                <textarea class="form-control" name="short_description" id="short_description" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editor1">Scholarship Description</label>
                                <textarea class="form-control" name="description" id="editor1" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image_path">Scholarship Feature Image</label>
                                <input name="image_path" type="file" accept="image/*" class="form-control-file" id="image_path">
                            </div>
                            <div class="form-group">
                                <label for="sponsor">Sponsor</label>
                                <input type="text" name="sponsor" class="form-control" id="sponsor" placeholder="Sponsor Name">
                            </div>
                            <div class="form-group">
                                <label for="sponsor_image_path">Scholarship Feature Image</label>
                                <input name="sponsor_image_path" type="file" accept="image/*" class="form-control-file" id="sponsor_image_path">
                            </div>
                            <div class="">
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
