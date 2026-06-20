{{-- <x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Confirm') }}
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
                        <h1 class="mb-0">Confirm Password</h1>
                        <p>This is a secure area of the application. Please confirm your password before continuing.</p>
                        <form class="mt-4 form-text" method="POST" action="{{ route('password.confirm') }}">
                            @csrf
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input required type="password" name="password" class="form-control mb-0" id="password"
                                    placeholder="Password">
                                @error('password')
                                    <div class="password"><span class="bg-primary text text-danger">{{ $message }}</span></div>
                                @enderror
                            </div>
                            <div class="sign-info text-center">
                                <button type="submit" class="btn btn-white d-block w-100 mb-2">Confirm</button>                                    
                            </div>
                        </form>
                    </div>
                </div>
                @include('auth.authside')
            </div>
        </div>
    </section>
@endsection