@extends('admin.index')
@section('title')
    Model Test Question
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Model Test Question</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">All Model Test Question {{ '(' . 'eventCount' . ')' }}</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Model Test</th>
                                    <th>Class</th>
                                    <th>Name</th>
                                    <th>R Ans</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modelquestionlist as $list)
                                    <tr>
                                        <td>{{$list->modeltest->name}}</td>
                                        <td>{{$list->modeltest->allclass->name}}</td>
                                        <td>{{$list->name}}</td>
                                        <td>{{'Option '.$list->option5}}</td>
                                        <td>
                                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                        data-toggle="dropdown">
                                                        <a href="" class="align-items-center"><i
                                                                class="ri-more-fill"></i></a>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdownMenuButton5">
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-eye-fill mr-2"></i>View</a>
                                                        <a class="dropdown-item" href="#"><i
                                                                class="ri-pencil-fill mr-2"></i>Edit</a>
                                                        <form method="POST" action="{{ route('mquestion.delete', $list->id) }}">
                                                            @csrf
                                                            @method('delete')
                                                            <button href="" class="dropdown-item"><i
                                                                    class="ri-delete-bin-6-fill mr-2"></i>Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="">
                            {{ $modelquestionlist->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Create Model Test Question</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="/modelquestion">
                            @csrf
                            <div class="form-group">
                                <label for="modeltest_id">Select Model Test</label>
                                <select required name="modeltest_id" id="modeltest_id" class="custom-select">
                                    @foreach ($modeltestlist as $list)
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
