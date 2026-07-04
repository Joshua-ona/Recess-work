<?php $__env->startSection('title', 'Groups'); ?>

<?php $__env->startSection('body'); ?>
<div style="display:flex; min-height:100vh; ">


       
    <?php echo $__env->make('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    
    <div class="dash-main" style="flex:1; width:100%; >

        
        <div class="dash-header" style="width:100%;" >
            <div>
                <div class="dash-header-title">Discussion Groups</div>
                <div class="dash-header-sub">Join a group to start discussing</div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/create" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus"></i> New Group
                </a>
            </div>
        </div>

        
        <div class="dash-body">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="dash-header-title" style="margin-bottom:12px;">My Groups</div>
            <div class="stat-grid" style="margin-bottom:30px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $myGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users-group"></i> Group
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        <?php echo e($group->name); ?>

                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        <?php echo e($group->description ?? 'No description'); ?>

                    </div>
                    <a href="/groups/<?php echo e($group->id); ?>" 
                       class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-arrow-right"></i> Open Group
                    </a>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-muted">You haven't joined any groups yet.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="dash-header-title" style="margin-bottom:12px;">Available Groups</div>
            <div class="stat-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users-group"></i> Group
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        <?php echo e($group->name); ?>

                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        <?php echo e($group->description ?? 'No description'); ?>

                    </div>
                    <a href="/groups/<?php echo e($group->id); ?>" 
                       class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-door-enter"></i> View & Join
                    </a>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-muted">No new groups available.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\fave\resources\views/groups/index.blade.php ENDPATH**/ ?>