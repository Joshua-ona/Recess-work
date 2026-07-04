<?php $__env->startSection('title', 'show'); ?>

<?php $__env->startSection('body'); ?>


    <?php echo $__env->make('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="dash-body">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $isMember = $group->users()->where('user_id', auth()->id())->exists();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMember): ?>
    <div class="stat-card" style="padding:24px; margin-bottom:20px;">
        <h3>Group Rules & Terms</h3>
        <p class="text-muted" style="margin:12px 0;">
            By joining this group, you agree to: post respectfully, avoid spamming unrelated content, 
            and respond to discussions in a timely manner. Repeated violations may result in warnings 
            or removal from the group.
        </p>
        <form method="POST" action="/groups/<?php echo e($group->id); ?>/join">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="ti ti-check"></i> I Agree & Join Group
            </button>
        </form>
    </div>
    <?php else: ?>
    <a href="/groups/<?php echo e($group->id); ?>/discussions" class="btn btn-primary btn-sm">
        <i class="ti ti-message-circle"></i> View Discussions
    </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\fave\resources\views/groups/show.blade.php ENDPATH**/ ?>