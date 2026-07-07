<?php $__env->startSection('title', 'Student Dashboard'); ?>

<?php $__env->startSection('body'); ?>

<div class="dash-wrap">


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
                <div class="dash-header-title">
                    Good <?php echo e(now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')); ?>,
                    <?php echo e(auth()->user()->first_name ?? auth()->user()->name); ?>

                </div>
                <div class="dash-header-sub">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($unanswered ?? 0) > 0): ?>
                    You have <?php echo e($unanswered); ?> unanswered <?php echo e(Str::plural('question', $unanswered)); ?>

                    <?php else: ?>
                    All caught up — great work!
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="dash-header-actions">
                <button class="icon-btn" aria-label="Search">
                    <i class="ti ti-search" aria-hidden="true"></i>
                </button>
                <a href="<?php echo e(route('student.discussions.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus" aria-hidden="true"></i> New post
                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="icon-btn" aria-label="Log out">
                        <i class="ti ti-logout" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>


            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--purple-50);">
                            <i class="ti ti-messages" style="color:var(--purple-600)" aria-hidden="true"></i>
                        </span>
                        Posts made
                    </div>
                    <div class="stat-value"><?php echo e($postCount ?? 0); ?></div>
                    <div class="stat-change text-pos">↑ <?php echo e($postsThisWeek ?? 0); ?> this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--pink-50);">
                            <i class="ti ti-heart" style="color:var(--pink-600)" aria-hidden="true"></i>
                        </span>
                        Upvotes received
                    </div>
                    <div class="stat-value"><?php echo e($upcomingQuiz ?? 0); ?></div>
                    <div class="stat-change text-pos">↑ <?php echo e($upComingQuizThisWeek ?? 0); ?> this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--teal-50);">
                            <i class="ti ti-books" style="color:var(--teal-400)" aria-hidden="true"></i>
                        </span>
                        Enrolled courses
                    </div>
                    <div class="stat-value"><?php echo e(($enrolledCourses ?? collect())->count()); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--amber-50);">
                            <i class="ti ti-star" style="color:var(--amber-400)" aria-hidden="true"></i>
                        </span>
                        Participation Score
                    </div>
                    <div class="stat-value"><?php echo e(number_format($ParticipationScore ?? 0)); ?></div>
                    <div class="stat-change text-pos">↑ <?php echo e($ParticipationGain ?? 0); ?> pts</div>
                </div>
            </div>


            <div class="panel-grid" style="grid-template-columns: 1fr 1fr 1fr; margin: 24px 0;">
                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title"><i class="ti ti-users"></i> My Groups</span>
                    </div>
                    <div class="panel-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myGroups->isEmpty()): ?>
                        <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No groups
                            yet. Request to create one.</p>
                        <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $myGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="disc-item">
                            <div
                                style="width:8px; height:8px; border-radius:50%; margin-top:4px; background:<?php echo e($g->status == 'approved' ? 'var(--green-600)' : 'var(--amber-600)'); ?>;">
                            </div>
                            <div style="flex:1;">
                                <a href="<?php echo e(route('student.groups.show', $g)); ?>" style="text-decoration:none;">
                                    <div class="disc-title"><?php echo e($g->name); ?></div>
                                </a>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title"><i class="ti ti-users"></i> Browse All Groups</span>
                    </div>
                    <div class="panel-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $browseGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="disc-item">
                            <div style="width:8px; height:8px; border-radius:50%; margin-top:4px; background:var(--green-600);">
                            </div>
                            <div style="flex:1;display:flex;justify-content:space-between;align-items:center;gap:8px;">
                                <div>
                                    <div class="disc-title"><?php echo e($g->name); ?></div>
                                    <div class="disc-meta">
                                        <span class="badge badge-green">approved</span>
                                    </div>
                                </div>

                                <form method="POST" action="<?php echo e(route('student.groups.join', $g)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="panel-action"
                                        style="border:none;background:none; color:var(--blue-600);cursor:pointer;">Join</button>
                                </form>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No
                            approved groups to join.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title"><i class="ti ti-circle-plus"></i> Create / Join Groups</span>
                    </div>
                    <div class="panel-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <p
                            style="font-size:13px;color:#065f46;background:#ecfdf5;padding:8px;border-radius:8px;margin-bottom:8px;">
                            <?php echo e(session('success')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <form method="POST" action="<?php echo e(route('student.groups.store')); ?>"
                            style="margin-bottom: 16px;">
                            <?php echo csrf_field(); ?>
                            <input name="name" value="<?php echo e(old('name')); ?>" placeholder="Group name e.g. Java"
                                style="width:100%; padding:8px; border:1px solid #e5e7eb;border-radius:8px; font-size:14px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p style="font-size:12px;color:#dc2626; margin-top:4px;"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <button class="btn btn-primary" style="width:100%; margin-top:8px;">Request
                                Group</button>
                            <p style="font-size:11px;color:var(--muted); margin-top:4px;">Needs admin approval.
                            </p>
                        </form>
                    </div>
                </div>
            </div>


                
                <div class="panel-grid">

                    
                    <div class="panel">
                        <div class="panel-head">
                            <span class="panel-title">Recent activity</span>
                            <a href="<?php echo e(route('student.discussions.index')); ?>" class="panel-action">See all</a>
                        </div>
                        <div class="panel-body">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivity ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="activity-item">
                                <div class="activity-dot"
                                    style="background:<?php echo e($activity->color ?? 'var(--purple-600)'); ?>">
                                </div>
                                <div>
                                    <div class="activity-text"><?php echo $activity->description; ?></div>
                                    <div class="activity-time">
                                        <span class="badge badge-<?php echo e($activity->badge_color ?? 'purple'); ?>">
                                            <?php echo e($activity->course_code ?? ''); ?>

                                        </span>
                                        <?php echo e($activity->created_at->diffForHumans()); ?>

                                    </div>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No
                                activity
                                yet. Start a discussion!</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="panel">
                        <div class="panel-head">
                            <span class="panel-title">Course discussions</span>
                            <a href="<?php echo e(route('student.discussions.index')); ?>" class="panel-action">View all</a>
                        </div>
                        <div class="panel-body">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $courseDiscussions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('student.discussions.show', $disc->id)); ?>"
                                style="text-decoration:none;display:block">
                                <div class="disc-item">
                                    <div style="flex:1;min-width:0">
                                        <div class="disc-title"><?php echo e($disc->title); ?></div>
                                        <div class="disc-meta">
                                            <span class="badge badge-purple"><?php echo e($disc->course->code ?? 'N/A'); ?></span>
                                            <?php echo e($disc->replies_count ?? 0); ?> replies
                                        </div>
                                        <div class="progress-bar" style="margin-top:6px">
                                            <div class="progress-fill" style="width:<?php echo e(min(100, $disc->engagement_pct ?? 0)); ?>%;
                                                        background:<?php echo e($disc->bar_color ?? 'var(--purple-600)'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">
                                No discussions in your courses yet.
                            </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                </div>

                
                <div class="full-panel">
                    <div class="panel-head">
                        <span class="panel-title">Trending this week</span>
                    </div>
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Course</th>
                                    <th>Replies</th>
                                    <th>Upvotes</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $trendingDiscussions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('student.discussions.show', $disc->id)); ?>"
                                            style="color:var(--text);text-decoration:none;font-weight:500">
                                            <?php echo e($disc->title); ?>

                                        </a>
                                    </td>
                                    <td><span class="badge badge-purple"><?php echo e($disc->course->code ?? 'N/A'); ?></span>
                                    </td>
                                    <td><?php echo e($disc->replies_count ?? 0); ?></td>
                                    <td><?php echo e($disc->upvotes_count ?? 0); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($disc->status === 'open'): ?>
                                        <span class="badge badge-green">Open</span>
                                        <?php elseif($disc->status === 'resolved'): ?>
                                        <span class="badge badge-amber">Resolved</span>
                                        <?php else: ?>
                                        <span class="badge badge-red">Closed</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--muted);padding:1.5rem">
                                        Nothing trending yet.</td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/student/dashboard.blade.php ENDPATH**/ ?>