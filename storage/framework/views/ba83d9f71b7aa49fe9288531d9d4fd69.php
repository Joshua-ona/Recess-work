
<?php $__env->startSection('title', 'Activate your account'); ?>

<?php $__env->startSection('body'); ?>
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

        <?php if($state === 'valid'): ?>

            <h1 class="auth-heading">Set your password</h1>
            <p class="auth-sub">Create a password to activate your lecturer account.</p>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger" role="alert">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/lecturer/activate/<?php echo e($token); ?>" novalidate>
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" type="password" name="password"
                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Min. 8 characters" required autocomplete="new-password" autofocus />
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

        <?php elseif($state === 'expired'): ?>

            <h1 class="auth-heading">Link has expired</h1>
            <p class="auth-sub">
                Your activation link is more than 3 days old. Enter your email
                below to get a fresh one — no need to contact the admin.
            </p>

            <?php if(session('status')): ?>
                <div class="alert alert-success" role="alert" style="margin:1rem 0;">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('lecturer.resend.self')); ?>" style="margin-top:1rem;">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="form-label" for="email">Your email address</label>
                    <input id="email" type="email" name="email"
                           value="<?php echo e($userEmail ?? ''); ?>"
                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="you@example.com" required autofocus />
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:0.75rem;width:100%;">
                    Send new activation link
                </button>
            </form>
            <p class="auth-footer" style="margin-top:1rem;">
                <a href="<?php echo e(route('login')); ?>">Back to sign in</a>
            </p>

        <?php else: ?> 

            <h1 class="auth-heading">Link not recognised</h1>
            <p class="auth-sub">
                This link doesn't match any account. It may have already been
                used to activate an account successfully.
            </p>
            <p class="auth-footer" style="margin-top:1rem;">
                <a href="<?php echo e(route('login')); ?>">Go to sign in</a>
            </p>

        <?php endif; ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/auth/activate-lecturer.blade.php ENDPATH**/ ?>