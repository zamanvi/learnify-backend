@extends('admin.index')
@section('title')
    Model Test Syllabus
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Model Test Syllabus</li>
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
                            <h4 class="card-title">Syllabus</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Modeltest Name</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modelSyllabuslist as $modelSyllabu)
                                    <tr>
                                        <td>{{ $modelSyllabu->modeltest->name }}</td>
                                        <td>{{ $modelSyllabu->name }}</td>
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
                                                        <a class="dropdown-item" href="{{ route('msyllabus.show', $modelSyllabu->id) }}"><i
                                                                class="ri-eye-fill mr-2"></i>View</a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('msyllabus.edit', $modelSyllabu->id) }}"><i
                                                                class="ri-pencil-fill mr-2"></i>Edit</a>
                                                        <form method="POST" action="{{ route('msyllabus.delete', $modelSyllabu->id) }}">
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
                            {{ $modelSyllabuslist->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('msyllabus.update', $modelSyllabus->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Syllabus Name</label>
                                <input required type="text" readonly name="name" class="form-control" id="name"
                                    value="{{ $modelSyllabus->name }}">
                            </div>
                            <div class="form-group">
                                <label for="modeltest_id">Model Test</label>
                                <select required name="modeltest_id" id="allmodeltest_idclass_id" class="custom-select">
                                    @foreach ($modelTestAll as $list)
                                        <option @selected($modelSyllabus->modeltest_id == $list->id) value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editor1">Syllabus Description</label>
                                <textarea class="form-control" name="description" id="editor1" rows="5">{{ $modelSyllabus->description }}</textarea>
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
