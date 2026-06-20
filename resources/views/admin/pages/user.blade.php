@extends('admin.index')
@section('title')
    User List
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All User</li>
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
                            <h4 class="card-title">Users {{ ($g_user + $t_user + $s_user) - $a_user }}</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Picture</th>
                                    <th>Name</th>
                                    <th>Redrose Id</th>
                                    <th>Email</th>
                                    <th>Points</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <img src="{{ $user->profile_photo_path != null ? $user->profile_photo_path : asset('images/user_01.jpg') }}" height="50" width="60"
                                                class="rounded-circle">
                                        </td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->redrose_id }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->points }}</td>
                                        <td>{{ $user->as_user }}</td>
                                        {{-- <td>
                                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                        data-toggle="dropdown">
                                                        <a href="" class="align-items-center"><i
                                                                class="ri-more-fill"></i></a>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdownMenuButton5">
                                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                                            data-target="#modalCenter"><i
                                                                class="ri-eye-fill mr-2"></i>View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>{{ $users->links() }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
