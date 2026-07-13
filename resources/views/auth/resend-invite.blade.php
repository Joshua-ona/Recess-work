@extends('layouts.app')
@section('title', 'Resend activation link')

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

        <h1 class="auth-heading">Resend activation link</h1>
        <p class="auth-sub">Enter the email address your admin used to invite you.</p>

        @if (session('status'))
            <div class="alert alert-success" role="alert" style="margin:1rem 0;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('lecturer.resend.self') }}" style="margin-top:1rem;">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="you@university.ac.ug" required autofocus />
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:0.75rem;">
                Send new activation link
            </button>
        </form>

        <p class="auth-footer" style="margin-top:1rem;">
            <a href="{{ route('login') }}">Back to sign in</a>
        </p>

    </div>
</div>
@endsection
