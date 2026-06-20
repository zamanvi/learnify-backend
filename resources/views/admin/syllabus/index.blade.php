@extends('admin.index')
@section('title')
    Events
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Syllabus</li>
        </ul>
    </nav>
@endsection
{{-- @section('script')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('js/ckeditor/styles.js') }}"></script>
    <script src="{{ asset('js/ckeditor/ckeditor.custom.js') }}"></script>
@endsection --}}
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.syllabus.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Create Syllabus for event</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('syllabus.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="event_id">Select Event</label>
                                <select required name="event_id" id="event_id" class="custom-select">
                                    @foreach ($eventlist as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Syllabus Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    placeholder="Syllabus Name">
                            </div>
                            <div class="form-group">
                                <label for="description">Syllabus Description</label>
                                <textarea class="form-control" name="description" id="description" rows="5"></textarea>
                                {{-- <textarea id="editor1" name="editor1" cols="30" rows="10"></textarea> --}}
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
