@extends('admin.index')
@section('title')
    Admin
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin - {{ $admin->name }}</li>
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
                            <h4 class="card-title">Admin {{ '(' . $a_user . ')' }}</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-4" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adminlist as $list)
                                    @if ($list->user_type == 1 || $list->user_type == 2)
                                    @else
                                        <tr>
                                            <td>{{ $list->name }}</td>
                                            <td>{{ $list->email }}</td>
                                            <td>
                                                <span class="badge iq-bg-info">
                                                    @if ($list->user_type == 3)
                                                        Event
                                                    @elseif ($list->user_type == 4)
                                                        Question
                                                    @elseif ($list->user_type == 5)
                                                        Support
                                                    @elseif ($list->user_type == 6)
                                                        Default
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="iq-card-header-toolbar d-flex align-items-center">
                                                    <div class="dropdown">
                                                        <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                            data-toggle="dropdown">
                                                            <a href=""><i class="ri-more-fill"></i></a>
                                                        </span>
                                                        <div class="dropdown-menu dropdown-menu-right"
                                                            aria-labelledby="dropdownMenuButton5">
                                                            <a class="dropdown-item"
                                                                href="/adminlist/{{ $list->id }}"><i
                                                                    class="ri-eye-fill mr-2"></i>View</a>
                                                            <form method="POST" action="/adminlist/{{ $list->id }}">
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
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Admin</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Admin <span class="text text-primary">{{ $admin->name }}</span></h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <h5 class="card-title">Name <span class="text text-primary">{{ $admin->name }}</span></h5>
                            <h5 class="card-title">Email <span class="text text-primary">{{ $admin->email }}</span></h5>
                            <h5 class="card-title">Admin Type <span class="text text-primary">
                                    @if ($admin->user_type == 3)
                                        Event
                                    @elseif ($admin->user_type == 4)
                                        Question
                                    @elseif ($admin->user_type == 5)
                                        Support
                                    @elseif ($admin->user_type == 6)
                                        Default
                                    @endif
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
