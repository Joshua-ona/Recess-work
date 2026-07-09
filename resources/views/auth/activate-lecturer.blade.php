@extends('layouts.app')
@section('title', 'Activate your account')

@section('body')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <div class="auth-logo-icon" aria-hidden="true">
                <i class="ti ti-messages"></i>
            </div>
            <div>
                <div class="auth-logo-name">EduDiscuss</div>
                <div class="auth-logo-sub">Academic discussion platform</div>
            </div>
        </div>

        @if ($state === 'valid')

            <h1 class="auth-heading">Set your password</h1>
            <p class="auth-sub">Create a password to activate your lecturer account.</p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/lecturer/activate/{{ $token }}" novalidate>
                @csrf
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 8 characters" required autocomplete="new-password" autofocus />
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group" style="margin-top:0.75rem;">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" placeholder="Repeat password"
                           required autocomplete="new-password" />
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:1rem;width:100%;">
                    Activate account
                </button>
            </form>

        @elseif ($state === 'expired')

            <h1 class="auth-heading">Link has expired</h1>
            <p class="auth-sub">
                Your activation link is more than 3 days old. Enter your email
                below to get a fresh one — no need to contact the admin.
            </p>

            @if (session('status'))
                <div class="alert alert-success" role="alert" style="margin:1rem 0;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('lecturer.resend.self') }}" style="margin-top:1rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Your email address</label>
                    <input id="email" type="email" name="email"
                           value="{{ $userEmail ?? '' }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="you@example.com" required autofocus />
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:0.75rem;width:100%;">
                    Send new activation link
                </button>
            </form>
            <p class="auth-footer" style="margin-top:1rem;">
                <a href="{{ route('login') }}">Back to sign in</a>
            </p>

        @else {{-- invalid --}}

            <h1 class="auth-heading">Link not recognised</h1>
            <p class="auth-sub">
                This link doesn't match any account. It may have already been
                used to activate an account successfully.
            </p>
            <p class="auth-footer" style="margin-top:1rem;">
                <a href="{{ route('login') }}">Go to sign in</a>
            </p>

        @endif

    </div>
</div>
@endsection
