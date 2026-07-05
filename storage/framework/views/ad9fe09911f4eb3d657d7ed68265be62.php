<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: -apple-system, Helvetica, Arial, sans-serif; background:#f5f5f5; padding:32px 0; margin:0;">
    <div style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:8px; padding:32px; border:1px solid #e5e5e5;">
        <p style="font-size:13px; color:#888; margin:0 0 4px;">EduDiscuss</p>
        <h2 style="margin:0 0 16px; font-size:20px;">You've been added as a lecturer</h2>

        <p style="font-size:14px; color:#333; line-height:1.5;">
            Hi <?php echo e($user->first_name); ?>,
        </p>
        <p style="font-size:14px; color:#333; line-height:1.5;">
            An administrator has created a lecturer account for you on EduDiscuss
            using this email address (<?php echo e($user->email); ?>). To activate your
            account, set a password using the button below.
        </p>

        <p style="text-align:center; margin:28px 0;">
            <a href="<?php echo e($activationUrl); ?>"
               style="background:#111827; color:#ffffff; padding:10px 20px; border-radius:6px; text-decoration:none; font-size:14px; display:inline-block;">
                Set your password
            </a>
        </p>

        <p style="font-size:12px; color:#999; line-height:1.5;">
            This link expires in 3 days. If you weren't expecting this, you can
            ignore this email — no account changes will be made until the link
            is used.
        </p>

        <p style="font-size:12px; color:#bbb; word-break:break-all; margin-top:16px;">
            Or copy this link: <?php echo e($activationUrl); ?>

        </p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/emails/lecturer-invitation.blade.php ENDPATH**/ ?>