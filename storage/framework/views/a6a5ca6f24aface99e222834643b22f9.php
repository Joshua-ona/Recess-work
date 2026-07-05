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
                    <?php if(($unanswered ?? 0) > 0): ?>
                        You have <?php echo e($unanswered); ?> unanswered <?php echo e(Str::plural('question', $unanswered)); ?>

                    <?php else: ?>
                        All caught up — great work!
                    <?php endif; ?>
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

        <div class="dash-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-messages" style="color:var(--purple-600)" aria-hidden="true"></i>
                        Posts made
                    </div>
                    <div class="stat-value"><?php echo e($postCount ?? 0); ?></div>
                    <div class="stat-change text-pos">↑ <?php echo e($postsThisWeek ?? 0); ?> this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-heart" style="color:var(--pink-600)" aria-hidden="true"></i>
                        Upvotes received
                    </div>
                    <div class="stat-value"><?php echo e($upcomingQuiz ?? 0); ?></div>
                    <div class="stat-change text-pos">↑ <?php echo e($upComingQuizThisWeek ?? 0); ?> this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-books" style="color:var(--teal-400)" aria-hidden="true"></i>
                        Enrolled courses
                    </div>
                    <div class="stat-value"><?php echo e(($enrolledCourses ?? collect())->count()); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-star" style="color:var(--amber-400)" aria-hidden="true"></i>
                        Participation Score
                    </div>
                    <div class="stat-value"><?php echo e(number_format($ParticipationScore?? 0)); ?></div>
                    <div class="stat-change text-pos">↑ <?php echo e($ParticipationGain ?? 0); ?> pts</div>
                </div>
            </div>

            
            <div class="panel-grid">

                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Recent activity</span>
                        <a href="<?php echo e(route('student.discussions.index')); ?>" class="panel-action">See all</a>
                    </div>
                    <div class="panel-body">
                        <?php $__empty_1 = true; $__currentLoopData = $recentActivity ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No activity yet. Start a discussion!</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Course discussions</span>
                        <a href="<?php echo e(route('student.discussions.index')); ?>" class="panel-action">View all</a>
                    </div>
                    <div class="panel-body">
                        <?php $__empty_1 = true; $__currentLoopData = $courseDiscussions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                            <div class="progress-fill"
                                                 style="width:<?php echo e(min(100, ($disc->engagement_pct ?? 0))); ?>%;
                                                        background:<?php echo e($disc->bar_color ?? 'var(--purple-600)'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">
                                No discussions in your courses yet.
                            </p>
                        <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $trendingDiscussions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('student.discussions.show', $disc->id)); ?>"
                                           style="color:var(--text);text-decoration:none;font-weight:500">
                                            <?php echo e($disc->title); ?>

                                        </a>
                                    </td>
                                    <td><span class="badge badge-purple"><?php echo e($disc->course->code ?? 'N/A'); ?></span></td>
                                    <td><?php echo e($disc->replies_count ?? 0); ?></td>
                                    <td><?php echo e($disc->upvotes_count ?? 0); ?></td>
                                    <td>
                                        <?php if($disc->status === 'open'): ?>
                                            <span class="badge badge-green">Open</span>
                                        <?php elseif($disc->status === 'resolved'): ?>
                                            <span class="badge badge-amber">Resolved</span>
                                        <?php else: ?>
                                            <span class="badge badge-red">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:1.5rem">Nothing trending yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/student/dashboard.blade.php ENDPATH**/ ?>