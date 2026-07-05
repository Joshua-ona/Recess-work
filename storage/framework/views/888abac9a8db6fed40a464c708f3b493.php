<?php $__env->startSection('body'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('Verify Your Email Address')); ?></div>

                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($error); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>   
                    <?php endif; ?>
                    
                    <p> <?php echo e(__('Please enter the 6 digit code sent to verifyyour email')); ?> </p>

                    <form class="d-inline" method="POST" action="<?php echo e(route('verification.check')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <input type="text" name="otp" class="form-control" maxlength="6" placeholder="Enter 6-digit verification code" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Verify</button>.
                    </form>

                    <hr>
                    <p> <?php echo e(__('if you did not receive the code,')); ?></p>
                    
                    <form class="d-inline" method="POST" action="<?php echo e(route('verification.send')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline"><?php echo e(__('click here to request for another')); ?></button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/auth/verify.blade.php ENDPATH**/ ?>