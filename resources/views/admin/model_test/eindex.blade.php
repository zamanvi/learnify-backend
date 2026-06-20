@extends('admin.index')
@section('title')
    All Model Test
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Class</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.model_test.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Edit Model Test</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('modeltest.update', $modeltest->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <input required type="text" readonly name="name" class="form-control" id="name" value="{{ $modeltest->name }}">
                            </div>
                            <div class="form-group">
                                <label for="allclass_id">Select Class</label>
                                <select required name="allclass_id" id="allclass_id" class="custom-select">
                                    <option value="{{ $modeltest->id }}">{{ $modeltest->allclass->name . ' Selected' }}</option>
                                    @foreach ($classlist as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input required type="text" name="subject" class="form-control" id="name"
                                    value="{{ $modeltest->subject }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Modeltest Status</label>
                                <select required name="type" id="type" class="custom-select">
                                    <option value="{{ $modeltest->type }}">{{ $modeltest->type . ' Selected' }}</option>
                                    <option value="on">On</option>
                                    <option value="off">Off</option>
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
