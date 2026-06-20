@extends('admin.index')
@section('title')
    Edit Contest
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Contest - {{ $contest->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.contest.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit Contest "{{ $contest->name }}"</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('contest.update', $contest->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Contest Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    value="{{ $contest->name }}">
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input required type="number" name="price" class="form-control" id="price"
                                    value="{{ $contest->price }}">
                            </div>
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <input required type="number" name="duration" class="form-control" id="duration"
                                    value="{{ $contest->duration }}">
                            </div>
                            <div class="form-group">
                                <label for="date">Select Date</label>
                                <input type="date" name="date" required class="form-control" id="date"
                                    value="{{ $contest->date }}">
                            </div>
                            <div class="form-group">
                                <label for="time">Select Time</label>
                                <input type="time" name="time" required class="form-control" id="time"
                                    value="{{ $contest->time }}">
                            </div>
                            <div class="form-group">
                                <label for="status">Select Contest statue</label>
                                <select required name="status" id="status" class="custom-select">
                                    <option @selected($contest->status == true) value="1">Active</option>
                                    <option @selected($contest->status != true) value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-control-file" for="customFile">Contest Feature Image</label>
                                <img height="100" width="100" src="{{ get_file($contest->image_path, 'contest') }}">
                                <input name="image_path" type="file" accept="image/*" class="form-control-file" id="customFile">
                            </div>

                            <div class="form-group">
                                <label for="syllabus_title">Syllabus Title</label>
                                <input type="text" name="syllabus_title" class="form-control" value="{{ $contest->syllabus_title }}">
                            </div>

                            <div class="form-group">
                                <label for="editor1">Syllabus Description</label>
                                <textarea class="form-control" name="description" id="editor1" rows="5">{{ $contest->description }}</textarea>
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
