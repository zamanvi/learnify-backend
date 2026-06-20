@extends('admin.index')
@section('title')
    {{ Auth::user()->name }}
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contest Results</li>
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
                            <h4 class="card-title">Contest Result list</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Picture</th>
                                    <th>Contest Name</th>
                                    <th>Name</th>
                                    <th>R Ans</th>
                                    <th>W Ans</th>
                                    <th>T Mark</th>
                                    <th>N Mark</th>
                                    <th>T Question</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>
                                            <img src="{{ $result->user->profile_photo_path != null ? $result->user->profile_photo_path : asset('images/user_01.jpg') }}"
                                                height="50" class="rounded-circle">
                                        </td>
                                        <td>{{ $result->contest->name }}</td>
                                        <td>{{ $result->user->name }}</td>
                                        <td>{{ $result->r_ans }}</td>
                                        <td>{{ $result->w_ans }}</td>
                                        <td>{{ $result->total_mark }}</td>
                                        <td>{{ $result->neg_mark }}</td>
                                        <td>{{ $result->total_q }}</td>
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
                                                        <a class="dropdown-item" href="#" data-toggle="modal"
                                                            data-target="#modalCenter"><i
                                                                class="ri-eye-fill mr-2"></i>View</a>
                                                    </div>
                                                    <div class="modal fade" id="modalCenter" tabindex="-1" role="dialog"
                                                        aria-labelledby="modalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="modalCenterTitle">
                                                                        Contest Name: {{ $result->contest->name }}
                                                                    </h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img src="{{ $result->contest->image_url != null ? $result->contest->image_url : '' }}"
                                                                        class="card-img-top" height="200" width="100"
                                                                        alt="Contest Image">

                                                                    <div class="row d-flex justify-content-between mt-3">
                                                                        <div class="col-lg-6 text-left">
                                                                            Participante Name
                                                                        </div>
                                                                        <div class="col-lg-6 text-right">
                                                                            {{ $result->user->name }}
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row d-flex justify-content-between">
                                                                        <div class="col-lg-6 text-left">
                                                                            Right Answered
                                                                        </div>
                                                                        <div class="col-lg-6 text-right">
                                                                            {{ $result->r_ans }}
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row d-flex justify-content-between">
                                                                        <div class="col-lg-6 text-left">
                                                                            Wrong Answered
                                                                        </div>
                                                                        <div class="col-lg-6 text-right">
                                                                            {{ $result->w_ans }}
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row d-flex justify-content-between">
                                                                        <div class="col-lg-6 text-left">
                                                                            Total Markes
                                                                        </div>
                                                                        <div class="col-lg-6 text-right">
                                                                            {{ $result->total_mark }}
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row d-flex justify-content-between">
                                                                        <div class="col-lg-6 text-left">
                                                                            Negative Markes
                                                                        </div>
                                                                        <div class="col-lg-6 text-right">
                                                                            {{ $result->neg_mark }}
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="row d-flex justify-content-between">
                                                                        <div class="col-lg-6 text-left">
                                                                            Total Question
                                                                        </div>
                                                                        <div class="col-lg-6 text-right">
                                                                            {{ $result->total_q }}
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>{{ $results->links() }}</div>
                </div>
            </div>

        </div>
    </div>
@endsection
