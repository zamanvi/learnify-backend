@extends('admin.index')
@section('title')
    {{ $viewType }} teacher
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="#">{{ $viewType . ' teacher'}}</a></li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title"> {{ $viewType }} Teacher ({{ $query->count() }})</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-4" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Degree</th>
                                    <th>Year</th>
                                    <th>Tution Class</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->title }}</td>
                                        <td>{{ $user->degree }}</td>
                                        <td>{{ $user->year }}</td>
                                        <td>{{ $user->tuition_class }}</td>
                                        <td>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.approval.teacher.edit', ['id' => $user->id, 'type' => $viewType]) }}"><i
                                                class="ri-eye-fill"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
