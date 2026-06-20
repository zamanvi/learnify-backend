@extends('admin.index')
@section('title')
    Support
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Support</li>
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
                            <h4 class="card-title">User Support List</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userlist as $list)
                                    <tr>
                                        <td class="text-center">
                                            @if ($list->user->profile_photo_path != null)
                                                <img class="img-fluid rounded-circle avatar-40"
                                                    src="{{ $list->user->profile_photo_path }}" alt="profile">
                                            @else
                                                <img class="img-fluid rounded-circle avatar-40" src="images/user_01.jpg"
                                                    alt="profile">
                                            @endif
                                        </td>
                                        <td>{{ $list->user->name }}</td>
                                        <td>{{ $list->support_replay->message }}</td>
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
                                                        <a class="dropdown-item" href="/event/{{ $list->id }}"><i
                                                                class="ri-eye-fill mr-2"></i>View</a>
                                                        <a class="dropdown-item" href="/event/{{ $list->id }}/edit"><i
                                                                class="ri-pencil-fill mr-2"></i>Edit</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="">
                            {{ $userlist->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Admin Support List</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adminlist as $list)
                                    <tr>
                                        <td class="text-center">
                                            @if ($list->user->profile_photo_path != null)
                                                <img class="img-fluid rounded-circle avatar-40"
                                                    src="{{ $list->user->profile_photo_path }}" alt="profile">
                                            @else
                                                <img class="img-fluid rounded-circle avatar-40" src="images/user_01.jpg"
                                                    alt="profile">
                                            @endif
                                        </td>
                                        <td>{{ $list->user->name }}</td>
                                        <td>{{ $list->support_replay->message }}</td>
                                        <td>
                                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle text-primary" id="admin"
                                                        data-toggle="dropdown">
                                                        <a href="" class="align-items-center"><i
                                                                class="ri-more-fill"></i></a>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="admin">
                                                        <a class="dropdown-item" href="/supportreplay/{{ $list->id }}"><i
                                                                class="ri-pencil-fill mr-2"></i>Replay</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="">
                            {{ $adminlist->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
