

<?php $__env->startSection('title', 'Add a lecturer'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between border-b pb-3 mb-6">
        <span class="font-medium text-lg">Add a lecturer</span>
        <a href="<?php echo e(route('admin.Users.index')); ?>" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
            ← Back to manage users
        </a>
    </div>

    <div class="bg-white border rounded-lg p-6 max-w-md">
        <p class="text-sm text-gray-500 mb-4">
            This creates the account and emails the lecturer an activation
            link. They'll set their own password and won't be able to sign
            in until they do.
        </p>

        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-md bg-red-50 text-red-700 text-sm px-4 py-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><?php echo e($error); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.lecturers.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm mb-1" for="first_name">First name</label>
                    <input id="first_name" type="text" name="first_name" value="<?php echo e(old('first_name')); ?>"
                           class="w-full border rounded px-3 py-2 text-sm" required autofocus>
                </div>
                <div>
                    <label class="block text-sm mb-1" for="last_name">Last name</label>
                    <input id="last_name" type="text" name="last_name" value="<?php echo e(old('last_name')); ?>"
                           class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
            </div>

            <div>
                <label class="block text-sm mb-1" for="email">Email address</label>
                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>"
                       class="w-full border rounded px-3 py-2 text-sm" placeholder="lecturer@university.ac.ug" required>
            </div>

            <button type="submit" class="text-sm bg-gray-900 text-white rounded px-4 py-2 hover:bg-gray-800">
                Send invitation
            </button>
        </form>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/admin/lecturers/create.blade.php ENDPATH**/ ?>