@extends('admin.index')
@section('title')
    Events
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Syllabus - {{ $syllabus->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.syllabus.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit Syllabus "{{ $syllabus->name }}"</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('syllabus.update', $syllabus->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="event_id" value="{{ $syllabus->event_id }}">
                            <div class="form-group">
                                <label for="name">Syllabus Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    value="{{ $syllabus->name }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Syllabus Description</label>
                                {{-- <textarea class="form-control" name="description" id="description" rows="5">{{ $syllabus->description }}</textarea> --}}
                                <textarea class="form-control" id="editor1" name="description" id="description" rows="5">{{ $syllabus->description }}</textarea>
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
