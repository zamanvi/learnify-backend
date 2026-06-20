@extends('admin.index')
@section('title')
    Contests
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Question</li>
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
                            <h4 class="card-title">Create Question for contest</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="/question">
                            @csrf
                            <div class="form-group">
                                <label for="contest_id">Select contest</label>
                                <select required name="contest_id" id="contest_id" class="custom-select">
                                    @foreach ($contestlist as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Question Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    placeholder="Question Name">
                            </div>
                            <div class="form-group">
                                <label for="option1">Option 1</label>
                                <input required type="text" name="option1" class="form-control" id="option1"
                                    placeholder="Option 1">
                            </div>
                            <div class="form-group">
                                <label for="option2">Option 2</label>
                                <input required type="text" name="option2" class="form-control" id="option2"
                                    placeholder="Option 2">
                            </div>
                            <div class="form-group">
                                <label for="option3">Option 3</label>
                                <input required type="text" name="option3" class="form-control" id="option3"
                                    placeholder="Option 3">
                            </div>
                            <div class="form-group">
                                <label for="option4">Option 4</label>
                                <input required type="text" name="option4" class="form-control" id="option4"
                                    placeholder="Option 4">
                            </div>
                            <div class="form-group">
                                <select required name="option5" class="custom-select">
                                    <option>Select Correct Option</option>
                                    <option value="1">Option 1</option>
                                    <option value="2">Option 2</option>
                                    <option value="3">Option 3</option>
                                    <option value="4">Option 4</option>
                                </select>
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
