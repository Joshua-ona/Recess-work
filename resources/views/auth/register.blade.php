@extends('layouts.app')
@section('title', 'Create account')

@section('body')

<div class="auth-page">
    <div class="auth-card">

```
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

    <h1 class="auth-heading">Create an account</h1>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        {{-- Name --}}
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="first_name">First name</label>
                <input
                    id="first_name"
                    type="text"
                    name="first_name"
                    class="form-control @error('first_name') is-invalid @enderror"
                    value="{{ old('first_name') }}"
                    placeholder="Leticia"
                    required
                    autocomplete="given-name"
                    autofocus
                />
                @error('first_name')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="last_name">Last name</label>
                <input
                    id="last_name"
                    type="text"
                    name="last_name"
                    class="form-control @error('last_name') is-invalid @enderror"
                    value="{{ old('last_name') }}"
                    placeholder="Namubiru"
                    required
                    autocomplete="family-name"
                />
                @error('last_name')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>

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
                    placeholder="namubiruleticiadiv@university.ac.ug"
                    required
                    autocomplete="email"
                />
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
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
                />
                @error('password')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
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

        {{-- Terms & Conditions --}}
        <div class="form-group">
            <label>
                <input
                    type="checkbox"
                    name="terms"
                    value="1"
                    required
                />
                I agree to the
                <a href="#">platform rules and terms</a>
            </label>

            @error('terms')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Create account
        </button>
    </form>

    <p class="auth-footer">
        Already registered?
        <a href="{{ route('login') }}">Sign in</a>
    </p>

</div>
```

</div>

@push('scripts')

<script>
    function selectRole(btn) {
        document.querySelectorAll('.role-btn').forEach(b => {
            b.classList.remove('selected');
            b.setAttribute('aria-pressed', 'false');
        });

        btn.classList.add('selected');
        btn.setAttribute('aria-pressed', 'true');

        const roleInput = document.getElementById('role-input');
        if (roleInput) {
            roleInput.value = btn.dataset.role;
        }
    }
</script>

@endpush
@endsection
