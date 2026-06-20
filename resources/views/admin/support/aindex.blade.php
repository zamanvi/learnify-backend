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
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-body chat-page p-0">
                        <div class="row">
                            <div class="col-lg-12 chat-data">
                                <div class="chat-content">
                                    @foreach ($support_replaylist as $list)
                                        @if ($list->sender_id == Auth::user()->id)
                                            <div class="chat">
                                                <div class="chat-user">
                                                    <a class="avatar m-0">
                                                        @if (Auth::user()->profile_photo_path != null)
                                                            <img class="img-fluid rounded-circle avatar-40"
                                                                src="{{ Auth::user()->profile_photo_path }}" alt="profile">
                                                        @else
                                                            <img class="img-fluid rounded-circle avatar-40" src="images/user_01.jpg"
                                                                alt="profile">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="chat-detail">
                                                    <div class="chat-message">
                                                        <p>{{ $list->message }}</p>
                                                        <span class="chat-time mt-1">{{ $list->created_at }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="chat chat-left">
                                                <div class="chat-user">
                                                    <a class="avatar m-0">
                                                        @if ($list->support->user->profile_photo_path != null)
                                                            <img class="img-fluid rounded-circle avatar-40"
                                                                src="{{ $list->support->user->profile_photo_path }}" alt="profile">
                                                        @else
                                                            <img class="img-fluid rounded-circle avatar-40" src="images/user_01.jpg"
                                                                alt="profile">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="chat-detail">
                                                    <div class="chat-message">
                                                        <p>{{ $list->message }}</p>
                                                        <span class="chat-time float-left">{{ $list->created_at }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="chat-footer p-3 bg-white">
                                    <form class="d-flex align-items-center" action="/supportreplay/{{ $support_user_id->support_id }}" method="POST">
                                        @csrf
                                        <input type="text" name="message" class="form-control mr-3"
                                            placeholder="Type your message">
                                        <input type="hidden" name="receiver_id" value="{{ $support_user_id->sender_id }}">
                                        <button type="submit" class="btn btn-primary d-flex align-items-center p-2">
                                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                                            <span class="d-none d-lg-block ml-1">Send</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
