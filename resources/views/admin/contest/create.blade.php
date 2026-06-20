@extends('admin.index')
@section('title')
    Contests
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Contest</li>
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
                            <h4 class="card-title">Create Contest</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('contest.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" class="form-control"
                                    placeholder="Contest Name" value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                    <div style="color: red;">
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="number" name="price" class="form-control digit"
                                    placeholder="Contest Price" value="{{ old('price') }}">
                                @if ($errors->has('price'))
                                    <div style="color: red;">
                                        {{ $errors->first('price') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="number" name="duration" class="form-control digit"
                                    placeholder="Exam time duration" value="{{ old('duration') }}">
                                @if ($errors->has('duration'))
                                    <div style="color: red;">
                                        {{ $errors->first('duration') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="date">Select Date</label>
                                <input type="date" name="date" class="form-control" id="date" value="{{ old('date') }}">
                                @if ($errors->has('date'))
                                    <div style="color: red;">
                                        {{ $errors->first('date') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="time">Select Time</label>
                                <input type="time" name="time" class="form-control" id="time" value="{{ old('time') }}">
                                @if ($errors->has('time'))
                                    <div style="color: red;">
                                        {{ $errors->first('time') }}
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="form-control-file" for="customFile">Contest Feature Image</label>
                                <input name="image_path" type="file" accept="image/*" class="form-control-file"
                                    id="customFile">
                            </div>


                            <div class="form-group">
                                <input type="text" name="syllabus_title" class="form-control"
                                    placeholder="Syllabus Title" value="{{ old('syllabus_title') }}">
                            </div>

                            <div class="form-group">
                                <label for="editor1">Syllabus Description</label>
                                <textarea class="form-control" name="description" id="editor1" rows="5">{{ old('description') }}</textarea>
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
