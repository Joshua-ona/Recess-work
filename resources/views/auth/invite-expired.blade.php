<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Invitation expired</title></head>
<body class="auth-page">
    <div class="auth-card" style="text-align:center;">
        <h2>This invitation link is invalid or has expired</h2>
        <p style="color:#6b6a66;">Please contact your system administrator to request a new invitation.</p>
        <a href="{{ route('login') }}" class="btn btn-primary" style="margin-top:1rem;">Go to login</a>
    </div>
</body>
</html>