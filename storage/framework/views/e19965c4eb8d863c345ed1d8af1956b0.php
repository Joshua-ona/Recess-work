<?php $__env->startSection('title', 'Admin dashboard'); ?>
<?php $__env->startSection('content'); ?>
<<<<<<< HEAD
<div class="flex items-center justify-between border-b pb-3 mb-6">
    <div class="flex items-center gap-3">
        <span class="font-medium text-lg">Admin dashboard</span>
        <span class="text-sm text-gray-500"><?php echo e(auth()->user()->full_name); ?></span>
=======

    <div class="flex items-center justify-between border-b pb-3 mb-6">
        <div class="flex items-center gap-3">
            <span class="font-medium text-lg">Admin dashboard</span>
            <span class="text-sm text-gray-500"><?php echo e(auth()->user()->full_name); ?></span>
        </div>
        <a href="<?php echo e(route('admin.Users.index')); ?>" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
            Manage all users
        </a>
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f
    </div>

    
</div>
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-gray-50 rounded-lg-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Total members</p>
        <p class="text-2xl font-semibold"><?php echo e($totalMembers); ?></p>
    </div>
    <div class="bg-gray-50 rounded-lg-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Active today</p>
        <p class="text-2xl font-semibold"><?php echo e($activeToday); ?></p>
    </div>
    <div class="bg-gray-50 rounded-lg-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Pending approvals</p>

        
    </div>
    <div class="bg-gray-50 rounded-lg-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Blacklisted</p>
        <p class="text-2xl font-semibold text-red-600"><?php echo e($blacklistedCount); ?></p>
    </div>
</div>
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <div class="bg-white border rounded-lg-lg p-5">
            <p class="font-semibold mb-3">Top
                contributors this month</p>
            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topContributors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span><?php echo e($c['name']); ?></ span>
                            <span class="text-gray-500"><?php echo e($c['posts']); ?>

                                posts</span>
                    </div>
                    <div class="h-1.5 bg-gray-50 rounded-lg-lg">
                        <div class="h-1.5 bg-green-600 rounded-lg" style="width: <?php echo e(min(100, $c['posts'] * 2.5)); ?>%">
                        </div>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            <p class="text-xs text-gray-400 mt-3">Placeholder data — wire up once the
                forum/post tables exist.</p>
        </div>
        <div class="bg-white border rounded-lg-lg p-5">
            <p class="font-medium mb-3">Flagged content</p>
            <div class="divide-y">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $flaggedContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex justify-between items-center py-2">
                    <div>
                        <p class="text-sm"><?php echo e($f['title']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($f['meta']); ?></p>
                    </div>
                    <button class="text-xs border rounded-lg px-2 py-1">Review</button>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-sm text-gray-500 py-2">Nothing flagged right
                    now.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="bg-white border rounded-lg-lg p-5">
            <p class="font-medium mb-3">Upcoming quiz configurations</p>
            <table class="w-full text-sm">
                <tr class="text-gray-500">
                    <td class="py-1">Quiz</td>
                    <td>Category</td>
                    <td class="text-right">Opens</ td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $upcomingQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="border-t">
                    <td class="py-2"><?php echo e($q['name']); ?></td>
                    <td><?php echo e($q['category']); ?></td>
                    <td class="text-right"><?php echo e($q['opens']); ?></td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </table>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white border rounded-lg-lg p-5">
            <p class="font-medium mb-3">Pending member approvals</p>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm"><?php echo e($user->full_name); ?></ span>
                        <div class="flex gap-1">
                            <form method="POST" action="<?php echo e(route('admin.Users.approve', $user)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="text-green-600 text-xs border rounded-lg px-2 py-1">Approve</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.Users.decline', $user)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="text-red-600 text-xs border rounded-lg px-2 py-1">Decline</button>
                            </form>
                        </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-sm text-gray-500">No pending approvals.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-5">
            <div class="flex items-center justify-between mb-3">
                
            </div>
<<<<<<< HEAD
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="border-b py-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold"><?php echo e($g->name); ?></p>
                        <p class="text-sm text-gray-500">By: <?php echo e($g->admin->name ?? 'Unknown'); ?> |<?php echo e($g->description); ?>

                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="<?php echo e(route('admin.groups.approve',$g)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="bg-green-600text-white px-3 py-1 rounded text-sm">Approve</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.groups.reject',$g)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="bg-red-600text-white px-3 py-1 roundedtext-sm">Reject</button>
                        </form>
                    </div>
=======

            <div class="bg-white border rounded-lg p-5">
                <p class="font-medium mb-3">Inactivity warnings</p>
                <div class="space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $warnedMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="py-2 border-t first:border-t-0">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm"><?php echo e($user->full_name); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status === 'blacklisted'): ?>
                                    <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">Blacklisted</span>
                                <?php else: ?>
                                    <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded">Warning <?php echo e($user->warning_count); ?> of 2</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="flex flex-col gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status === 'blacklisted'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.Users.unblacklist', $user)); ?>" class="flex justify-end">
                                        <?php echo csrf_field(); ?>
                                        <button class="text-xs border rounded px-2 py-1">Reinstate</button>
                                    </form>
                                <?php else: ?>
                                    <details>
                                        <summary class="text-xs border rounded px-2 py-1 inline-block cursor-pointer select-none">Warn</summary>
                                        <form method="POST" action="<?php echo e(route('admin.Users.warn', $user)); ?>" class="mt-2 space-y-1">
                                            <?php echo csrf_field(); ?>
                                            <textarea name="message" rows="2" required placeholder="Describe the rule violation…"
                                                      class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                            <button class="text-xs border rounded px-2 py-1 w-full">Send warning</button>
                                        </form>
                                    </details>
                                    <div class="flex gap-1 justify-end">
                                        <form method="POST" action="<?php echo e(route('admin.Users.logout', $user)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button class="text-xs border rounded px-2 py-1">Log out</button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('admin.Users.blacklist', $user)); ?>"
                                              onsubmit="return confirm('Blacklist <?php echo e($user->full_name); ?> immediately?');">
                                            <?php echo csrf_field(); ?>
                                            <button class="text-xs border rounded px-2 py-1 text-red-600">Blacklist</button>
                                        </form>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-sm text-gray-500 py-2">No members currently flagged for inactivity.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-gray-500">No pending groups.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="bg-white border rounded-lg p-5">
        <p class="font-medium mb-3">Inactivity warnings</p>
        <div class="space-y-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $warnedMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="py-2 border-t first:border-t-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm"><?php echo e($user->full_name); ?></ span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status ==='blacklisted'): ?>
                        <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">Blacklisted</span>
                        <?php else: ?>
                        <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded">Warning
                            <?php echo e($user->warning_count); ?> of 2</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="flex flex-col gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->status ==='blacklisted'): ?>
                    <form method="POST" action="<?php echo e(route('admin.Users.unblacklist',$user)); ?>" class="flex justify-end">
                        <?php echo csrf_field(); ?>
                        <button class="text-xs border rounded px-2py-1">Reinstate</button>
                    </form>
                    <?php else: ?>
                    <details>
                        <summary class="text-xs border rounded px-2 py-1 inline-block cursor-pointer select-none">
                            Warn
                        </summary>
                        <form method="POST" action="<?php echo e(route('admin.Users.warn',$user)); ?>" class="mt-2 space-y-1">
                            <?php echo csrf_field(); ?>
                            <textarea name="message" rows="2" required placeholder="Describe the rule violation…"
                                class="w-full border rounded px-2 py-1 text-xs"></textarea>
                            <button class="text-xs border rounded px-2 py-1 w-full">Send warning</button>
                        </form>
                    </details>
                    <div class="flex gap-1 justify-end">
                        <form method="POST" action="<?php echo e(route('admin.Users.logout',$user)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="text-xs border rounded px-2 py-1">Log out</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.Users.blacklist',$user)); ?>"
                            onsubmit="return confirm('Blacklist <?php echo e($user->full_name); ?>immediately?');">
                            <?php echo csrf_field(); ?>
                            <button class="text-xs border rounded px-2 py-1 text-red-600">Blacklist</button>
                        </form>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <p class="text-sm text-gray-500 py-2">No members currently flagged for inactivity.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <div class="bg-white borderrounded-lg p-5">
        <p class="font-medium mb-3">ML-recommended trending topics</p>
        <div class="flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $trendingTopics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded"><?php echo e($t); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <p class="text-xs text-gray-400 mt-3">Placeholder — swap in real output from the recommendation
            microservice.
        </p>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\fave\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>