<?php $__env->startSection('title', 'Group Discussions'); ?>

<?php $__env->startSection('body'); ?>
<div style="display:flex; min-height:100vh, flex-direction:column;">


       
    <?php echo $__env->make('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title"><?php echo e($group->name); ?></div>
                <div class="dash-header-sub">Discussions in this group</div>
            </div>
            <div class="dash-header-actions">
    <a href="/groups/<?php echo e($group->id); ?>/stats" class="btn btn-outline btn-sm">
        <i class="ti ti-chart-bar"></i> Statistics
    </a>
    <a href="/groups/<?php echo e($group->id); ?>/discussions/create" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> New Discussion
    </a>
</div>
        </div>

        <div class="dash-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="stat-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $group->discussions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discussion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-message-circle"></i> 
                        <?php echo e($discussion->user->first_name ?? 'Unknown'); ?>

                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        <?php echo e($discussion->title); ?>

                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        <?php echo e(Str::limit($discussion->body, 100)); ?>

                    </div>
                    <a href="/groups/<?php echo e($group->id); ?>/discussions/<?php echo e($discussion->id); ?>" 
                       class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-arrow-right"></i> View Discussion
                    </a>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-muted">No discussions yet. Start one!</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\fave\resources\views/groups/discussions.blade.php ENDPATH**/ ?>