@extends('admin.index')
@section('title')
    Admin
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Admin</li>
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
                            <h4 class="card-title">Admin {{'(' . $a_user . ')'}}</h4>
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
                                                        Model Test Admin
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
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton5">
                                                            <a class="dropdown-item" href="/adminlist/{{$list->id}}"><i class="ri-eye-fill mr-2"></i>View</a>
                                                            <form method="POST" action="/adminlist/{{$list->id}}">
                                                                @csrf
                                                                @method('delete')
                                                                <button href="" class="dropdown-item"><i class="ri-delete-bin-6-fill mr-2"></i>Delete</button>
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
                            <h4 class="card-title">Create Admin</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <form method="POST" action="{{ route('createadmin') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Admin Name</label>
                                <input required type="text" name="name" class="form-control" id="name"
                                    placeholder="Enter Name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input required type="email" name="email" class="form-control" id="email"
                                    placeholder="Email address">
                            </div>
                            <div class="form-group">
                                <label for="user_type">Select admin type</label>
                                <select required name="user_type" id="user_type" class="custom-select">
                                    <option value="3">Event Admin</option>
                                    <option value="4">Model Test Admin</option>
                                    <option value="5">Support admin</option>
                                    <option value="6">Default admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="class">Select Model  admin's class</label>
                                <select required name="class" id="class" class="custom-select">
                                    <option>Select Class</option>
                                    @foreach ($allclasslist as $list)
                                    <option value="{{$list->id}}">{{$list->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input required type="password" name="password" class="form-control mb-0" id="password"
                                    placeholder="password">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input required type="password" name="password_confirmation" class="form-control mb-0"
                                    id="password_confirmation" placeholder="Confirm Password">
                            </div>
                            <input checked type="checkbox" hidden name="terms" class="custom-control-input">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
