@extends('layouts.guest2')
@section('title')
    Registration
@endsection
@section('content')
    <!-- Sign in Start -->
    {{-- <section class="sign-in-page">
        <div class="container p-0">
            <div class="row no-gutters height-self-center">
                <div class="col-sm-12 align-self-center page-content rounded">
                    <div class="row m-0">
                        <div class="col-sm-12 sign-in-page-data">
                            <div class="sign-in-from bg-info rounded">
                                <h3 class="mb-0 text-center text-white">Sign Up</h3>
                                <p class="text-center text-white">Enter your email address and password to access dashboard panel.</p>
                                <form class="mt-4 form-text" method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Your Full Name</label>
                                        <input required type="name" name="name" class="form-control mb-0" id="name"
                                            placeholder="Your Full Name">
                                        @if ($errors->any())
                                            <div class=""><span>{{ $error->name }}</span></div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input required type="email" name="email" class="form-control mb-0" id="email"
                                            placeholder="Enter email">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input required type="password" name="password" class="form-control mb-0" id="password"
                                            placeholder="password">
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input required type="password" name="password_confirmation" class="form-control mb-0" id="password_confirmation"
                                            placeholder="Confirm Password">
                                    </div>
                                    <div class="d-inline-block w-100">
                                        <div class="custom-control custom-checkbox d-inline-block mt-2 pt-1">
                                            <input required type="checkbox" name="terms" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">I accept <a
                                                    href="#" class="text-light">Terms and Conditions</a></label>
                                        </div>
                                    </div>
                                    <div class="sign-info text-center">
                                        <button type="submit" class="btn btn-white d-block w-100 mb-2">Sign Up</button>
                                        <span class="text-dark d-inline-block line-height-2">Already Have Account ? <a
                                                href="{{ route('login') }}" class="text-white">Log In</a></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <section class="sign-in-page" style="margin-top: 10%">
        <div class="container-fluid p-0">
            {{-- @if ($errors->any())
                <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif --}}

            <div class="row no-gutters">
                <div class="col-sm-6 align-self-center">
                    <div class="sign-in-from">
                        <h1 class="mb-0">Sign in</h1>
                        <p>Enter your email address and password to access admin panel.</p>
                        <form class="mt-4 form-text" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Your Full Name</label>
                                <input required type="name" name="name" class="form-control mb-0" id="name"
                                    placeholder="Your Full Name" value="{{ old('name') }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input required type="email" name="email" class="form-control mb-0" id="email"
                                    placeholder="Enter email" value="{{ old('email') }}">
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
                            <div class="d-inline-block w-100">
                                <div class="custom-control custom-checkbox d-inline-block mt-2 pt-1">
                                    <input required type="checkbox" name="terms" class="custom-control-input"
                                        id="customCheck1">
                                    <label class="custom-control-label" for="customCheck1">I accept <a href="#"
                                            class="text-primary">Terms and Conditions</a></label>
                                </div>
                            </div>
                            <div class="sign-info text-center">
                                <button type="submit" class="btn btn-primary d-block w-100 mb-2">Sign Up</button>
                                <span class="text-dark d-inline-block line-height-2">Already Have Account ? <a
                                        href="{{ route('login') }}" class="text-primary">Log In</a></span>
                            </div>
                            @include('layouts.flash-message')
                        </form>
                    </div>
                </div>
                @include('auth.authside')
            </div>
        </div>
    </section>
    <!-- Sign in END -->
@endsection
