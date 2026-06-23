@extends('layouts.app')
@section('title', 'Sign in')

@section('body')
<div class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <div class="auth-logo-icon" aria-hidden="true">
                <i class="ti ti-messages"></i>
            </div>
            <div>
                <div class="auth-logo-name">EduDiscuss</div>
                <div class="auth-logo-sub">Academic discussion platform</div>
            </div>
        </div>

        <h1 class="auth-heading">Welcome back</h1>
        <p class="auth-sub">Sign in to continue your discussions</p>

        {{-- Session errors --}}
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Status message (e.g. password reset) --}}
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <div class="input-wrap">
                    <i class="ti ti-mail" aria-hidden="true"></i>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="you@university.ac.ug"
                        required
                        autocomplete="email"
                        autofocus
                    />
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <i class="ti ti-lock" aria-hidden="true"></i>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember / forgot --}}
            <div class="remember-row">
                <label class="form-check">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-sm">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>

        @if (Route::has('register'))
            <p class="auth-footer">No account? <a href="{{ route('register') }}">Create one</a></p>
        @endif

    </div>
</div>
@endsection