@extends('layouts.guest2')
@section('title')
    Login
@endsection
@section('content')
    <section class="sign-in-page" style="margin-top: 10%">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-6 align-self-center">
                    <div class="sign-in-from">
                        <h1 class="mb-0">Sign in</h1>
                        <p>Enter your email address and password to access admin panel.</p>
                        <form class="mt-4 orm-text" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" name="email" class="form-control mb-0" id="email"
                                    placeholder="Enter email or RedRoseId" value="{{ old('email') }}">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <a href="{{ route('password.request') }}" class="float-right">Forgot password?</a>
                                <input type="password" name="password" class="form-control mb-0" id="password"
                                    placeholder="Password">
                            </div>
                            <div class="d-inline-block w-100">
                                <div class="custom-control custom-checkbox d-inline-block mt-2 pt-1">
                                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                                    <label class="custom-control-label" for="customCheck1">Remember Me</label>
                                </div>
                                <button type="submit" class="btn btn-primary float-right">Sign in</button>
                            </div>
                            <span class="text-dark dark-color d-inline-block line-height-2">Don't have an
                                account? <a href="{{ route('register') }}" class="text-primary">Sign up</a></span>
                            @include('layouts.flash-message')
                        </form>
                    </div>
                </div>
                @include('auth.authside')
            </div>
        </div>
    </section>
@endsection
