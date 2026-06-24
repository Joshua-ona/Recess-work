@extends('layouts.app')
@section('title', 'Create account')

@section('body')
<div class="auth-page">
    <div class="auth-card">
        {{-- T&Cs Modal --}}
<div id="tcs-modal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;">
    <div style="background:white;padding:30px;border-radius:10px;max-width:500px;width:90%;max-height:80vh;overflow-y:auto;">
        <h2>Platform Rules & Terms</h2>
        <br>
        <p><strong>1.</strong> Post only relevant academic content.</p>
        <p><strong>2.</strong> Respect all members at all times.</p>
        <p><strong>3.</strong> Do not share personal information of others.</p>
        <p><strong>4.</strong> Inactive members will receive warnings before being blacklisted.</p>
        <p><strong>5.</strong> Quizzes must be attempted honestly without assistance.</p>
        <p><strong>6.</strong> Moderators have authority to remove inappropriate content.</p>
        <br>
        <button onclick="document.getElementById('tcs-modal').style.display='none'" style="background:#1a3c8f;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;width:100%;">
            I Agree — Continue to Register
        </button>
    </div>
</div>

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
        <p class="auth-sub">Choose your role to get started</p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            {{-- Role selector --}}
            <div class="form-group">
                <label class="form-label">I am a</label>
                <div class="role-selector" role="group" aria-label="Select role">
                    @foreach (['admin' => ['ti-shield-check', 'Admin'], 'student' => ['ti-school', 'Student'], 'lecturer' => ['ti-chalkboard', 'Lecturer']] as $value => [$icon, $label])
                        <button
                            type="button"
                            class="role-btn {{ old('role', 'student') === $value ? 'selected' : '' }}"
                            data-role="{{ $value }}"
                            onclick="selectRole(this)"
                            aria-pressed="{{ old('role', 'student') === $value ? 'true' : 'false' }}"
                        >
                            <i class="ti {{ $icon }}" aria-hidden="true"></i>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" id="role-input" name="role" value="{{ old('role', 'student') }}" />
                @error('role')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

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
                        placeholder="Aisha"
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
                        placeholder="Nakato"
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
                        placeholder="you@university.ac.ug"
                        required
                        autocomplete="email"
                    />
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            {{-- Student / Staff ID --}}
            <div class="form-group">
                <label class="form-label" for="student_id">Student / Staff ID</label>
                <div class="input-wrap">
                    <i class="ti ti-id-badge" aria-hidden="true"></i>
                    <input
                        id="student_id"
                        type="text"
                        name="student_id"
                        class="form-control @error('student_id') is-invalid @enderror"
                        value="{{ old('student_id') }}"
                        placeholder="e.g. 2023/CS/001"
                        required
                    />
                </div>
                @error('student_id')
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

            {{--T&Cs Checkbox--}}
            <div class="form-group">
                <label>
                    <input
                    type="checkbox"
                    name="agreed_rules"
                    value="1"
                    required
                    />
                    I agree to the
                    <a href="#">platform rules and terms</a>
</label>
@error('agreed_rules')
<span class="invalid-feedback" role="alert">{{$message}}</span>
@enderror
</div>

            <button type="submit" class="btn btn-primary">Create account</button>
        </form>

        <p class="auth-footer">Already registered? <a href="{{ route('login') }}">Sign in</a></p>

    </div>
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
        document.getElementById('role-input').value = btn.dataset.role;
    }
</script>
@endpush
@endsection