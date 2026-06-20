@extends('layouts.admin')
@section('title')
    All Participant
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title"> All Participent </h4>
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
                                        <th>Contest</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contestEnrolls as $contestEnroll)
                                        <tr>
                                            <td class="text-center">
                                                <img class="img-fluid rounded-circle avatar-40" src="{{ $contestEnroll->user->profile_photo_path != null ? $contestEnroll->user->profile_photo_path : asset('images/user_01.jpg')}}" alt="profile">
                                            </td>
                                            <td>{{ $contestEnroll->user->name }}</td>
                                            <td>{{ $contestEnroll->contest->name }}</td>
                                            <td>{{ $contestEnroll->contest->status ? 'Active' : 'Inactive'; }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div>{{ $contestEnrolls->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
