@extends('admin.index')
@section('title')
    Support
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Support Create</li>
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
                                    <p class="text text-white bg-primary">You do not have any access please request admin to give an permission.</p>
                                </div>
                                <div class="chat-footer p-3 bg-white">
                                    <form class="d-flex align-items-center" action="/support" method="POST">
                                        @csrf
                                        <input type="text" name="message" class="form-control mr-3" placeholder="Type your message">
                                        <input type="hidden" name="receiver_id" value="1" >
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
