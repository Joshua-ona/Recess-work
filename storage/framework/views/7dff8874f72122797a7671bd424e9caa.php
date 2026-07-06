<?php $__env->startSection('title', 'New Discussion'); ?>

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
                <div class="dash-header-title">New Discussion</div>
                <div class="dash-header-sub">in <?php echo e($group->name); ?></div>
            </div>
        </div>

        <div class="dash-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/groups/<?php echo e($group->id); ?>/discussions">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control"
                        placeholder="Discussion title" value="<?php echo e(old('title')); ?>" required>
                </div>

                <div class="form-group" style="margin-top:16px;">
                    <label>Body</label>
                    <textarea name="body" class="form-control"
                        rows="6" placeholder="Write your discussion here..." required><?php echo e(old('body')); ?></textarea>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-send"></i> Post Discussion
                    </button>
                    <a href="/groups/<?php echo e($group->id); ?>/discussions" class="btn btn-outline btn-sm">
                        <i class="ti ti-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/groups/discussions-create.blade.php ENDPATH**/ ?>