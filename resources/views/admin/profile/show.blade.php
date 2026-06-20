@extends('admin.index')
@section('title')
    {{ Auth::user()->name }}
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            @if (Auth::user()->user_type == 1)
                <li class="breadcrumb-item active" aria-current="page">Super Admin Profile</li>
            @else
                <li class="breadcrumb-item active" aria-current="page">Admin Profile</li>
            @endif
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row profile-content">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="iq-card">
                    <div class="iq-card-body profile-page">
                        <div class="profile-header">
                            <div class="cover-container text-center">
                                @if ($user->profile_photo_path != null)
                                    <img src="{{ $user->profile_photo_path }}" class="rounded-circle img-fluid">
                                @else
                                    <img src="{{ asset('images/user_01.jpg') }}"class="rounded-circle img-fluid">
                                @endif
                            </div>

                            <div class="profile-detail mt-3 mb-3">
                                <h3>{{ $user->name }}</h3>
                            </div>
                            <div class="iq-card-body m-0 p-0">
                                <ul class="list-inline p-0 mb-0">
                                    <li>
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-sm-4">
                                                <h6>Bio</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="mb-0">{{ $user->bio }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-sm-4">
                                                <h6>RedRose Id</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="mb-0">{{ $user->redrose_id }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-sm-4">
                                                <h6>Designation</h6>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="mb-0">{{ $user->designation }}</p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between align-items-center mb-0">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0">Personal Details</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <ul class="list-inline p-0 mb-0">
                            <li>
                                <div class="row align-items-center justify-content-between mb-3">
                                    <div class="col-sm-3">
                                        <h6>Birthday</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="mb-0">{{ $user->birthday }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row align-items-center justify-content-between mb-3">
                                    <div class="col-sm-3">
                                        <h6>Address</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="mb-0">{{ $user->address }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row align-items-center justify-content-between mb-3">
                                    <div class="col-sm-3">
                                        <h6>Phone</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="mb-0">{{ $user->phone }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="row align-items-center justify-content-between mb-3">
                                    <div class="col-sm-3">
                                        <h6>Email</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="mb-0">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between align-items-center mb-0">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0">About</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <p>{{ $user->about }}</p>
                    </div>
                </div>
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between align-items-center mb-0">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0">Social Information</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <ul class="list-inline d-flex p-0 mb-2 align-items-center">
                            @if ($user->whatsapp != null)
                                <li>
                                    <a class="btn btn-primary" href="{{ $user->whatsapp }}" target="blank">
                                        <h6 class="text text-white">Whatsapp</h6>
                                    </a>
                                </li>
                            @endif
                            @if ($user->facebook != null)
                                <li class="ml-2">
                                    <a class="btn btn-primary" href="{{ $user->facebook }}" target="blank">
                                        <h6 class="text text-white">Facebook</h6>
                                    </a>
                                </li>
                            @endif
                            @if ($user->twitter != null)
                                <li class="ml-2">
                                    <a class="btn btn-primary" href="{{ $user->twitter }}" target="blank">
                                        <h6 class="text text-white">Twitter</h6>
                                    </a>
                                </li>
                            @endif
                            @if ($user->instagram != null)
                                <li class="ml-2">
                                    <a class="btn btn-primary" href="{{ $user->instagram }}" target="blank">
                                        <h6 class="text text-white">Instagram</h6>
                                    </a>
                                </li>
                            @endif
                        </ul>
                        <ul class="list-inline d-flex p-0 mb-0 align-items-center">
                            @if ($user->linkedin != null)
                                <li>
                                    <a class="btn btn-primary" href="{{ $user->linkedin }}" target="blank">
                                        <h6 class="text text-white">LinkedIn</h6>
                                    </a>
                                </li>
                            @endif
                            @if ($user->pinterest != null)
                                <li class="ml-2">
                                    <a class="btn btn-primary" href="{{ $user->pinterest }}" target="blank">
                                        <h6 class="text text-white">Pinterest</h6>
                                    </a>
                                </li>
                            @endif
                            @if ($user->tiktok != null)
                                <li class="ml-2">
                                    <a class="btn btn-primary" href="{{ $user->tiktok }}" target="blank">
                                        <h6 class="text text-white">Tiktok</h6>
                                    </a>
                                </li>
                            @endif
                            @if ($user->wechat != null)
                                <li class="ml-2">
                                    <a class="btn btn-primary" href="{{ $user->wechat }}" target="blank">
                                        <h6 class="text text-white">Wechat</h6>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <form method="POST" action="/profile" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="iq-card">
                                <div class="iq-card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">User Information</h4>
                                    </div>
                                </div>
                                <div class="iq-card-body">
                                    <div class="new-user-info">
                                        <div class="row">
                                            <div class="form-group text-center col-md-12">
                                                <div class="add-img-user profile-img-edit">
                                                    @if ($user->profile_photo_path != null)
                                                        <img class="profile-pic img-fluid"
                                                            src="{{ $user->profile_photo_path }}" alt="profile-pic">
                                                    @else
                                                        <img class="profile-pic img-fluid"
                                                            src="{{ asset('images/user_01.jpg') }}" alt="profile-pic">
                                                    @endif
                                                    <div class="p-image">
                                                        <a href="javascript:void();"
                                                            class="upload-button btn iq-bg-primary">Upload
                                                            Photo</a>
                                                        <input class="file-upload" name="profile_photo" type="file"
                                                            accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="img-extension mt-3">
                                                    <div class="d-inline-block align-items-center">
                                                        <span>Only</span>
                                                        <span>.jpg</span>
                                                        <span>.png</span>
                                                        <span>.jpeg</span>
                                                        <span>allowed</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 mt-3">
                                                <label for="redrose_id">RedRose Id</label>
                                                @if ($redrose_id_edit == 'on')
                                                    <span> {{ '(you are availlable to edit)' }}</span>
                                                    <input type="text" name="redrose_id" class="form-control"
                                                        id="redrose_id" value="{{ $user->redrose_id }}">
                                                @else
                                                    <span>{{ '(last edit on ' . $user->date . ')' }}</span>
                                                    <input class="form-control" readonly id="redrose_id"
                                                        value="{{ $user->redrose_id }}">
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6 mt-3">
                                                <label for="bio">Bio:</label>
                                                <input type="text" name="bio" class="form-control" id="bio"
                                                    value="{{ $user->bio }}">
                                            </div>
                                            <div class="form-group col-md-6 mt-3">
                                                <label for="designation">Designation:</label>
                                                <input type="text" name="designation" class="form-control"
                                                    id="designation" value="{{ $user->designation }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="birthday">Date of Birth Date</label>
                                                <input type="date" name="birthday"
                                                    value="{{ $user->birthday }}" class="form-control"
                                                    id="birthday">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="name">Name:</label>
                                                <input type="text" name="name" class="form-control" id="name"
                                                    value="{{ $user->name }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="email"> Email: </label>
                                                <input id="email" class="form-control" readonly
                                                    value="{{ $user->email }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="add1">Address:</label>
                                                <input type="text" required name="address" class="form-control"
                                                    id="add1" value="{{ $user->address }}">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label>Country:</label>
                                                <select class="form-control" required name="country" id="selectcountry">
                                                    <option value="{{ $user->country }}">
                                                        {{ $user->country }} Selected</option>
                                                    <option value="Bangladesh">Bangladesh</option>
                                                    <option value="Chin">Chin</option>
                                                    <option value="India">India</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="company_name">Company Name:</label>
                                                <input type="text" name="company_name" class="form-control"
                                                    id="company_name" value="{{ $user->company_name }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="mobno">Mobile Number:</label>
                                                <input type="number" required name="phone" class="form-control"
                                                    id="mobno" value="{{ $user->phone }}">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="about">About You:</label>
                                                <textarea class="form-control" name="about" id="about" rows="2">{{ $user->about }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="iq-card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">Social Link</h4>
                                    </div>
                                </div>
                                <div class="iq-card-body">
                                    <div class="new-user-info">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="whatsapp">Whatsapp Number:</label>
                                                <input type="number" name="whatsapp" class="form-control"
                                                    id="whatsapp" value="{{ $user->whatsapp }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="facebook">Facebook:</label>
                                                <input type="text" name="facebook" class="form-control"
                                                    id="facebook" value="{{ $user->facebook }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="twitter">Twitter:</label>
                                                <input type="text" name="twitter" class="form-control" id="twitter"
                                                    value="{{ $user->twitter }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="instagram">Instagram:</label>
                                                <input type="text" name="instagram" class="form-control"
                                                    id="instagram" value="{{ $user->instagram }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="linkedin">Linkedin:</label>
                                                <input type="text" name="linkedin" class="form-control"
                                                    id="linkedin" value="{{ $user->linkedin }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="pinterest">Pinterest:</label>
                                                <input type="text" name="pinterest" class="form-control"
                                                    id="pinterest" value="{{ $user->pinterest }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="tiktok">Tiktok:</label>
                                                <input type="text" name="tiktok" class="form-control" id="tiktok"
                                                    value="{{ $user->tiktok }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="wechat">Wechat:</label>
                                                <input type="text" name="wechat" class="form-control" id="wechat"
                                                    value="{{ $user->wechat }}">
                                            </div>
                                        </div>
                                        <hr>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="reset" class="btn btn-warning">Cancel</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
