@extends('admin.index')
@section('title')
    Teacher Approval
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="#">{{ $type }}</a></li>
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
                            <h4>Profile Info</span></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <span>Name: <span>{{ $user->name }}</span></span> <br>
                        <span>Point: <span>{{ $user->points }}</span></span> <br>
                        <span>Id: <span>{{ $user->redrose_id }}</span></span> <br>
                        <span>Date Of Birth: <span>{{ $user->birthday }}</span></span> <br>
                        <span>Phone: <span>{{ $user->phone }}</span></span> <br>
                        <span>Email: <span>{{ $user->email }}</span></span> <br>
                        <span>Gender: <span>{{ $user->gender }}</span></span>
                    </div>
                </div>

                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4>Latest Educational Info</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <span>Degree: <span>{{ $user->degree }}</span></span> <br>
                        <span>Year: <span>{{ $user->year }}</span></span> <br>
                        <span>Group/Subject: <span>{{ $user->group_subject }}</span></span> <br>
                        <span>Institute: <span>{{ $user->institute }}</span></span> <br>
                        <span>result: <span>{{ $user->result }}</span></span>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4>Tuition Area</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <span>Area Country: <span>{{ $user->area_country }}</span></span> <br>
                        <span>Area Division: <span>{{ $user->area_division }}</span></span> <br>
                        <span>Area City: <span>{{ $user->area_city }}</span></span> <br>
                        <span>Area Upazila: <span>{{ $user->area_upazila }}</span></span> <br>
                        <span>Area Post Office: <span>{{ $user->area_post_office }}</span></span> <br>
                        <span>Area Union: <span>{{ $user->area_union }}</span></span> <br>
                        <span>Area Village: <span>{{ $user->area_village }}</span></span> <br>
                        <span>Area Road House: <span>{{ $user->area_road_house }}</span></span>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4>Tuition Details</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <span>Monthly Salary: <span>{{ $user->expected_salary }}</span></span> <br>
                        <span>Period Class: <span>{{ $user->period_class }}</span></span> <br>
                        <span>Duration: <span>{{ $user->duration }}</span></span> <br>
                        <span>Place Of LLearning: <span>{{ $user->place_of_learning }}</span></span> <br>
                        <span>Through Of Learning: <span>{{ $user->through_of_learning }}</span></span> <br>
                        <span>Tuition Type: <span>{{ $user->tuition_type }}</span></span> <br>
                        <span>Class of students: <span>{{ $user->tuition_class }}</span></span> <br>
                        <span>Tuition Subject: <span>{{ $user->tuition_subject }}</span></span> <br>
                        <span>Tuition Time: <span>{{ $user->tuition_time }}</span></span> <br>
                        <span>Medium: <span>{{ $user->medium }}</span></span> <br>
                        <span>Current Status For Tuition: <span>{{ $user->status_for_tuition }}</span></span>
                        {{-- <br>
                        <span>Remark: <span>{{ $user->remark }}</span></span> --}}
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4>Contact</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <span>Whatsapp: <span>{{ $user->whatsapp }}</span></span> <br>
                        <span>Facebook: <span>{{ $user->facebook }}</span></span> <br>
                        <span>Twitter: <span>{{ $user->twitter }}</span></span> <br>
                        <span>Instagram: <span>{{ $user->instagram }}</span></span> <br>
                        <span>Linkedin: <span>{{ $user->linkedin }}</span></span> <br>
                        {{-- <br>
                        <span>Remark: <span>{{ $user->remark }}</span></span> --}}
                    </div>
                </div>

                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4>Tutor Details</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <span>Teaching Subject / title: <span>{{ $user->title }}</span></span> <br>

                        <form method="POST" action="{{ route('admin.approval.teacher.approve', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mt-2">
                                <div class="col-md-6">
                                    <label for="title_description">Teaching Subject Description:</label>
                                    <textarea class="form-control" name="title_description" id="title_description" rows="3">{{ $user->title_description }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="about_teacher">About Teacher:</label>
                                    <textarea class="form-control" name="about_teacher" id="about_teacher" rows="3">{{ $user->about_teacher }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="about_teaching">Teaching Experience:</label>
                                    <textarea class="form-control" name="about_teaching" id="about_teaching" rows="3">{{ $user->about_teaching }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" type="text" name="remark" id="remark" rows="3">{{ $user->remark }}</textarea>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <input required class="ml-2" @checked($user->update_status == '1') type="radio" name="type" value="1"
                                    id="approve" />
                                <label for="approve">Approve</label>
                                <input required class="ml-2" @checked($user->update_status == '3') type="radio" name="type" value="3"
                                    id="unapprove" />
                                <label for="unapprove">Unapprove</label>
                                <input required class="ml-2" @checked($user->update_status == '2') type="radio" name="type" value="2"
                                    id="pending" />
                                <label for="pending">Pending</label>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
