

<nav class="sidebar" aria-label="Main navigation">

    <div class="sidebar-logo">
        <div class="sidebar-logo-row">
            <div class="sidebar-logo-icon" aria-hidden="true">
                <i class="ti ti-messages"></i>
            </div>
            <div>
                <div class="sidebar-logo-name">EduDiscuss</div>
                <div class="sidebar-logo-sub">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'admin'): ?> Administration
                    <?php elseif($role === 'lecturer'): ?> Lecturer portal
                    <?php else: ?> Student portal
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'admin'): ?>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Overview</div>
            <a href="<?php echo e(route('admin.dashboard')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i> Dashboard
            </a>
            <a href="<?php echo e(route('admin.analytics')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.analytics') ? 'active' : ''); ?>">
                <i class="ti ti-chart-bar" aria-hidden="true"></i> Analytics
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Management</div>
            <a href="<?php echo e(route('admin.users.index')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>">
                <i class="ti ti-users" aria-hidden="true"></i> Users
                <span class="sidebar-badge"><?php echo e($userCount ?? 0); ?></span>
            </a>
            <a href="<?php echo e(route('admin.discussions')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.discussions*') ? 'active' : ''); ?>">
                <i class="ti ti-messages" aria-hidden="true"></i> Discussions
            </a>
            <a href="<?php echo e(route('admin.courses')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.courses*') ? 'active' : ''); ?>">
                <i class="ti ti-books" aria-hidden="true"></i> Courses
            </a>
            <a href="<?php echo e(route('admin.reports')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.reports*') ? 'active' : ''); ?>">
                <i class="ti ti-flag" aria-hidden="true"></i> Reports
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($openReports ?? 0) > 0): ?>
                    <span class="sidebar-badge"><?php echo e($openReports); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">System</div>
            <a href="<?php echo e(route('admin.settings')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('admin.settings') ? 'active' : ''); ?>">
                <i class="ti ti-settings" aria-hidden="true"></i> Settings
            </a>
            
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sidebar-item logout-btn">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </div>

    
    <?php elseif($role === 'lecturer'): ?>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Overview</div>
            <a href="<?php echo e(route('lecturer.dashboard')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('lecturer.dashboard') ? 'active' : ''); ?>">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i> Dashboard
            </a>
            
            <a href="<?php echo e(route('lecturer.discussions.index')); ?>" class="sidebar-item">
                <i class="ti ti-messages"></i> Discussions
            </a>

            <a href="<?php echo e(route('lecturer.categories')); ?>" class="sidebar-item">
                <i class="ti ti-category"></i> Categories
            </a>

            <a href="<?php echo e(route('lecturer.quizzes')); ?>" class="sidebar-item">
                <i class="ti ti-clipboard-check"></i> Quizzes
            </a>
            <a href="<?php echo e(route('lecturer.performance')); ?>" class="sidebar-item">
                <i class="ti ti-chart-bar"></i> Performance Reports
            </a>

            <a href="<?php echo e(route('chat')); ?>" class="sidebar-item">
                <i class="ti ti-message-circle"></i> Messages
            </a>

            <a href="<?php echo e(route('lecturer.notifications')); ?>" class="sidebar-item">
                <i class="ti ti-bell"></i> Notifications
            </a>

            <a href="<?php echo e(route('lecturer.pinned')); ?>" class="sidebar-item">
                <i class="ti ti-bookmark"></i> Pinned Topics
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">My courses</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $myCourses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('lecturer.courses.show', $course->id)); ?>"
                   class="sidebar-item <?php echo e(request()->is('lecturer/courses/'.$course->id.'*') ? 'active' : ''); ?>">
                    <i class="ti ti-calculator" aria-hidden="true"></i>
                    <?php echo e($course->code); ?> — <?php echo e(Str::limit($course->name, 18)); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <a href="<?php echo e(route('lecturer.courses.create')); ?>" class="sidebar-item">
                <i class="ti ti-plus" aria-hidden="true"></i> Create course
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Tools</div>
            <a href="<?php echo e(route('lecturer.pinned')); ?>" class="sidebar-item">
                <i class="ti ti-pin" aria-hidden="true"></i> Pinned threads
            </a>
            <a href="<?php echo e(route('lecturer.flagged')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('lecturer.flagged') ? 'active' : ''); ?>">
                <i class="ti ti-flag" aria-hidden="true"></i> Flagged posts
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($flaggedCount ?? 0) > 0): ?>
                    <span class="sidebar-badge"><?php echo e($flaggedCount); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sidebar-item logout-btn">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </div>

    
    <?php else: ?>
        <div class="sidebar-section">
            <div class="sidebar-section-label">My space</div>
            <a href="<?php echo e(route('student.dashboard')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('student.dashboard') ? 'active' : ''); ?>">
                <i class="ti ti-home" aria-hidden="true"></i> Home
            </a>
            <a href="<?php echo e(route('student.discussions.index')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('student.discussions.*') ? 'active' : ''); ?>">
                <i class="ti ti-messages" aria-hidden="true"></i> My discussions
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($unreadCount ?? 0) > 0): ?>
                    <span class="sidebar-badge"><?php echo e($unreadCount); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <a href="<?php echo e(route('student.saved')); ?>" class="sidebar-item">
                <i class="ti ti-bookmark" aria-hidden="true"></i> Saved posts
            </a>
            <a href="<?php echo e(route('student.quizzes')); ?>" class="sidebar-item">
                <i class="ti ti-clipboard-check"></i> Quizzes
            </a>

            <a href="<?php echo e(route('chat')); ?>" class="sidebar-item">
                <i class="ti ti-message-circle"></i> Messages
            </a>

            <a href="<?php echo e(route('groups.index')); ?>" class="sidebar-item">
                <i class="ti ti-users-group"></i> Groups
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Courses</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $enrolledCourses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('student.course', $course->id)); ?>"
                   class="sidebar-item <?php echo e(request()->is('student/courses/'.$course->id.'*') ? 'active' : ''); ?>">
                    <i class="ti ti-book" aria-hidden="true"></i>
                    <?php echo e($course->code); ?> — <?php echo e(Str::limit($course->name, 18)); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <a href="<?php echo e(route('student.courses.browse')); ?>" class="sidebar-item">
                <i class="ti ti-plus" aria-hidden="true"></i> Browse all courses
            </a>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Account</div>
            
            <a href="<?php echo e(route('student.notifications')); ?>" class="sidebar-item">
                <i class="ti ti-bell" aria-hidden="true"></i> Notifications
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($notifCount ?? 0) > 0): ?>
                    <span class="sidebar-badge"><?php echo e($notifCount); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sidebar-item logout-btn">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="sidebar-spacer"></div>

    
    <div class="sidebar-user">
        <div class="sidebar-avatar" aria-hidden="true">
            <?php echo e(strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr($user->last_name ?? '', 0, 1))); ?>

        </div>
        <div>
            <div class="sidebar-user-name"><?php echo e($user->name ?? ($user->first_name.' '.$user->last_name)); ?></div>
            <div class="sidebar-user-meta"><?php echo e($user->email); ?></div>
        </div>
    </div>
     
    <div class="header">
           <a href="<?php echo e(route('student.dashboard')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('student.dashboard') ? 'active' : ''); ?>">
                <i class="ti ti-home" aria-hidden="true"></i> 
            </a>
            <a href="<?php echo e(route('student.discussions.index')); ?>"
               class="sidebar-item <?php echo e(request()->routeIs('student.discussions.*') ? 'active' : ''); ?>">
                <i class="ti ti-messages" aria-hidden="true"></i> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($unreadCount ?? 0) > 0): ?>
                    <span class="sidebar-badge"><?php echo e($unreadCount); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <a href="<?php echo e(route('student.saved')); ?>" class="sidebar-item">
                <i class="ti ti-bookmark" aria-hidden="true"></i>  
            </a>
             <a href="<?php echo e(route('student.quizzes')); ?>" class="sidebar-item">
                <i class="ti ti-clipboard-check"></i> 
            </a>
            <a href="<?php echo e(route('chat')); ?>" class="sidebar-item">
                <i class="ti ti-message-circle"></i> 
            </a>
            <a href="<?php echo e(route('groups.index')); ?>" class="sidebar-item">
                <i class="ti ti-users-group"></i> 
            </a>
             <div class="sidebar-avatar" aria-hidden="true">
            <?php echo e(strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr($user->last_name ?? '', 0, 1))); ?>

        </div>

    </div>

</nav><?php /**PATH C:\xampp\htdocs\fave\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>