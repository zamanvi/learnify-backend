{{-- <x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout> --}}

@extends('layouts.guest2')
@section('title')
    Forget Password
@endsection
@section('content')
    <section class="sign-in-page">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-sm-6 align-self-center">
                    <div class="sign-in-from">
                        <h3 class="mb-0 text-center text-white">Foeget Password</h3>
                        <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
                        <form class="mt-4 form-text" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input required type="email" name="email" class="form-control mb-0" id="email"
                                    placeholder="Enter email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="error"><span class="bg-primary text text-danger">{{ $message }}</span></div>
                                @enderror
                            </div>
                            <div class="sign-info text-center">
                                <button type="submit" class="btn btn-primary text-white d-block w-100 mb-2">Email Password Reset Link</button>
                                <span class="text-dark dark-color d-inline-block line-height-2">Don't have an
                                    account? <a href="{{ route('register') }}" class="text-primary">Sign up</a></span>
                            </div>
                            @include('layouts.flash-message')
                        </form>
                    </div>
                </div>
                @include('auth.authside')
            </div>
        </div>
    </section>
@endsection