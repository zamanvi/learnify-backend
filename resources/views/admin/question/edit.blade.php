@extends('admin.index')
@section('title')
    Contests
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Question - {{ $contestQuestion->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.question.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit Question "{{ $contestQuestion->name }}"</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('contest.question.update', $contestQuestion->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="contest_id" value="{{ $contestQuestion->contest_id }}">
                            <div class="form-group">
                                <input required type="text" name="name" class="form-control" id="name"
                                    value="{{ $contestQuestion->name }}">
                            </div>
                            <div class="form-group">
                                <input required type="text" name="option1" class="form-control" id="option1"
                                    value="{{ $contestQuestion->option1 }}">
                            </div>
                            <div class="form-group"><input required type="text" name="option2" class="form-control"
                                    id="option2" value="{{ $contestQuestion->option2 }}"></div>
                            <div class="form-group"><input required type="text" name="option3" class="form-control"
                                    id="option3" value="{{ $contestQuestion->option3 }}"></div>
                            <div class="form-group"><input required type="text" name="option4" class="form-control"
                                    id="option4" value="{{ $contestQuestion->option4 }}"></div>
                            <div class="form-group">
                                <label for="option5">Select Correct answer</label>
                                <select required name="option5" class="custom-select">
                                    <option @selected($contestQuestion->option5 == '1') value="1">Option 1</option>
                                    <option @selected($contestQuestion->option5 == '2') value="2">Option 2</option>
                                    <option @selected($contestQuestion->option5 == '3') value="3">Option 3</option>
                                    <option @selected($contestQuestion->option5 == '4') value="4">Option 4</option>
                                </select>
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
