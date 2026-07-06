

<?php $__env->startSection('title', 'Manage users'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between border-b pb-3 mb-6">
        <div class="flex items-center gap-3">
            <span class="font-medium text-lg">Manage users</span>
            <span class="text-sm text-gray-500"><?php echo e($users->total()); ?> total</span>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.lecturers.create')); ?>" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
                + Add lecturer
            </a>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
                ← Back to dashboard
            </a>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('admin.Users.index')); ?>" class="mb-4">
        <input
            type="text"
            name="search"
            value="<?php echo e($search); ?>"
            placeholder="Search by name or email…"
            class="w-full max-w-sm border rounded px-3 py-2 text-sm"
        >
    </form>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Logged in</th>
                    <th class="px-4 py-2">Warnings</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo e($user->full_name); ?></td>
                        <td class="px-4 py-2 text-gray-500"><?php echo e($user->email); ?></td>
                        <td class="px-4 py-2 capitalize"><?php echo e($user->role); ?></td>
                        <td class="px-4 py-2">
                            <?php if($user->status === 'blacklisted'): ?>
                                <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">Blacklisted</span>
                            <?php elseif($user->status === 'pending'): ?>
                                <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded">Pending</span>
                            <?php else: ?>
                                <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2">
                            <?php if(in_array($user->id, $onlineIds)): ?>
                                <span class="inline-flex items-center gap-1 text-xs text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Online
                                </span>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">Offline</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2">
                            <?php if($user->warnings->isEmpty()): ?>
                                <span class="text-xs text-gray-400"><?php echo e($user->warning_count); ?> of 2</span>
                            <?php else: ?>
                                <details>
                                    <summary class="text-xs text-amber-700 cursor-pointer select-none">
                                        <?php echo e($user->warning_count); ?> of 2 — view
                                    </summary>
                                    <div class="mt-2 space-y-2 max-w-xs">
                                        <?php $__currentLoopData = $user->warnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="text-xs border rounded px-2 py-1.5 bg-amber-50">
                                                <p><?php echo e($warning->message); ?></p>
                                                <p class="text-gray-400 mt-1">
                                                    <?php echo e($warning->issuer?->full_name ?? 'Unknown admin'); ?> ·
                                                    <?php echo e($warning->created_at->diffForHumans()); ?>

                                                </p>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <?php if($user->id === auth()->id()): ?>
                                <span class="text-xs text-gray-400">You</span>
                            <?php else: ?>
                                <div class="flex flex-col gap-2 items-end">
                                    <?php if($user->status === 'blacklisted'): ?>
                                        <form method="POST" action="<?php echo e(route('admin.Users.unblacklist', $user)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button class="text-xs border rounded px-2 py-1">Reinstate</button>
                                        </form>
                                    <?php else: ?>
                                        
                                        <?php if($user->role === 'lecturer' && $user->status === 'pending'): ?>
                                            <form method="POST" action="<?php echo e(route('admin.lecturers.resend', $user)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button class="text-xs border rounded px-2 py-1 text-blue-600">Resend invite</button>
                                            </form>
                                        <?php endif; ?>
                                        <details>
                                            <summary class="text-xs border rounded px-2 py-1 inline-block cursor-pointer select-none">Warn</summary>
                                            <form method="POST" action="<?php echo e(route('admin.Users.warn', $user)); ?>" class="mt-2 space-y-1 w-48">
                                                <?php echo csrf_field(); ?>
                                                <textarea name="message" rows="2" required placeholder="Describe the rule violation…"
                                                          class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                                <button class="text-xs border rounded px-2 py-1 w-full">Send warning</button>
                                            </form>
                                        </details>
                                        <div class="flex gap-1">
                                            <?php if(in_array($user->id, $onlineIds)): ?>
                                                <form method="POST" action="<?php echo e(route('admin.Users.logout', $user)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <button class="text-xs border rounded px-2 py-1">Log out</button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?php echo e(route('admin.Users.blacklist', $user)); ?>"
                                                  onsubmit="return confirm('Blacklist <?php echo e($user->full_name); ?> immediately?');">
                                                <?php echo csrf_field(); ?>
                                                <button class="text-xs border rounded px-2 py-1 text-red-600">Blacklist</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($users->links()); ?>

    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/admin/users/index.blade.php ENDPATH**/ ?>