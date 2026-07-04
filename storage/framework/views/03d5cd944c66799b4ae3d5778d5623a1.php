<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Admin dashboard'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/tailwind.css'); ?>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="max-w-6xl mx-auto px-6 py-6">
        <div class="flex items-center justify-end gap-3 mb-4 text-sm text-gray-500">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <span><?php echo e(auth()->user()->full_name); ?></span>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="border rounded px-3 py-1 text-xs hover:bg-gray-50">
                    Log out
                </button>
            </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
        <div class="mb-4 rounded-md bg-green-50 text-green-800 text-sm px-4 py-2">
            <?php echo e(session('status')); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
</body>

</html><?php /**PATH C:\xampp\htdocs\fave\resources\views/layouts/admin.blade.php ENDPATH**/ ?>