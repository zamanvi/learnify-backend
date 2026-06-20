@extends('layouts.guest2')
@section('title')
    Resend Verification Email
@endsection
@section('content')
    <section class="sign-in-page">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-sm-6 align-self-center">
                    <div class="sign-in-from">
                        <h3 class="mb-0 text-center text-white">Email Verify</h3>
                        <p>Before continuing, could you verify your email address by clicking on the link we just emailed to
                            you? If you didn\'t receive the email, we will gladly send you another.</p>
                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
                            </div>
                        @endif
                        <form class="mt-4 form-text" method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <div class="sign-info text-center">
                                <button type="submit" class="btn btn-white d-block w-100 mb-2">Resend Verification
                                    Email</button>
                            </div>
                        </form>
                        <div class="m-4"></div>
                        <form class="mt-4 form-text" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <div class="sign-info text-center">
                                <button type="submit" class="btn btn-white d-block w-100 mb-2">Logout</button>
                            </div>
                        </form>
                    </div>
                </div>
                @include('auth.authside')
            </div>
        </div>
    </section>
@endsection
