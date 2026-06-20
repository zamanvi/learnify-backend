@extends('layouts.admin')
@section('title')
    Notification
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Notification List</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <div class="table-responsive">
                            <table id="user-list-table" class="table table-striped table-bordered mt-4" role="grid"
                                aria-describedby="user-list-page-info">
                                <thead>
                                    <tr>
                                        <th>Profile</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notificationlist as $list)
                                        <tr>
                                            <td class="text-center">
                                                @if ($list->user->profile_photo_path != null)
                                                <img class="img-fluid rounded-circle avatar-40" src="{{ $list->user->profile_photo_path }}" alt="profile">
                                                @else
                                                <img class="img-fluid rounded-circle avatar-40" src="images/user_01.jpg" alt="profile">
                                                @endif
                                            </td>
                                            <td>{{ $list->name }}</td>
                                            <td>{{ $list->date }}</td>
                                            <td>{{ $list->time }}</td>
                                            <td>{{ $list->type }}</td>
                                            <td>
                                                @if ($list->status == 1)
                                                    <span class="badge bg-warning">Unread</span>
                                                @else
                                                    <span class="badge bg-info">Read</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex align-items-center list-user-action">

                                                </div>
                                                <div class="iq-card-header-toolbar d-flex align-items-center">
                                                    <div class="dropdown">
                                                        <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                            data-toggle="dropdown">
                                                            <a href="" class="align-items-center"><i
                                                                    class="ri-more-fill"></i></a>
                                                        </span>
                                                        @if ($list->status == 1)
                                                            <div class="dropdown-menu dropdown-menu-right"
                                                                aria-labelledby="dropdownMenuButton5">
                                                                <form action="/notification/{{ $list->id }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="dropdown-item"><i
                                                                            class="las la-check mr-2"></i>Mark as
                                                                        Read</button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <div class="dropdown-menu dropdown-menu-right"
                                                                aria-labelledby="dropdownMenuButton5">
                                                                <a class="dropdown-item" href="#"><i class="las la-check-double mr-2"></i>Readed</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="">{{ $notificationlist->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
