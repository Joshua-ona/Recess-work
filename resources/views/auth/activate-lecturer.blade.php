@extends('layouts.app')
@section('title', 'Activate your account')

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

        @if ($invalid)
            <h1 class="auth-heading">Link invalid or expired</h1>
            <p class="auth-sub">
                This activation link is no longer valid. Ask your admin to
                send you a new invitation.
            </p>
            <p class="auth-footer"><a href="{{ route('login') }}">Back to sign in</a></p>
        @else
            <h1 class="auth-heading">Activate your account</h1>
            <p class="auth-sub">Set a password to finish setting up your lecturer account</p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('lecturer.activate.store', ['token' => $token]) }}" novalidate>
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                            autofocus
                        />
                        @error('password')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Activate account</button>
            </form>
        @endif

    </div>
</div>
@endsection
