<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Activate your lecturer account</title>
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon"><i class="ti ti-affiliate"></i></div>
            <div>
                <div class="auth-logo-name">EduDiscuss</div>
                <div class="auth-logo-sub">Lecturer activation</div>
            </div>
        </div>

        <div class="auth-heading">Welcome, {{ $user->first_name }}</div>
        <div class="auth-sub">Set a password to activate your lecturer account.</div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left:1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('lecturer.activate.complete', $token) }}">
            @csrf

            <div class="form-group">
                <label class="form-label">New password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>

            <div class="form-group">
                <label class="form-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem;">
                Activate account
            </button>
        </form>
    </div>
</body>
</html>