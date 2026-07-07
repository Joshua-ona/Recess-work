<?php $__env->startSection('title', 'Lecturer Dashboard'); ?>

<?php $__env->startSection('body'); ?>
<div class="dash-wrap">

    
    <?php echo $__env->make('layouts.sidebar', [
        'role'         => 'lecturer',
        'user'         => auth()->user(),
        'myCourses'    => $myCourses   ?? collect(),
        'flaggedCount' => $flaggedCount ?? 0,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="dash-main">

        
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    Good <?php echo e(now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')); ?>,
                    Dr. <?php echo e(auth()->user()->last_name ?? auth()->user()->name); ?>

                </div>
                <div class="dash-header-sub">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($flaggedCount ?? 0) > 0): ?>
                        <?php echo e($flaggedCount); ?> flagged <?php echo e(Str::plural('post', $flaggedCount)); ?> need<?php echo e($flaggedCount === 1 ? 's' : ''); ?> your attention
                    <?php else: ?>
                        All clear — no flagged posts
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="dash-header-actions">
                <button class="icon-btn" aria-label="Search">
                    <i class="ti ti-search" aria-hidden="true"></i>
                </button>
                <a href="<?php echo e(route('lecturer.announcements.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="ti ti-speakerphone" aria-hidden="true"></i> Post announcement
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

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users" style="color:var(--purple-600)" aria-hidden="true"></i>
                        My students
                    </div>
                    <div class="stat-value"><?php echo e($studentCount ?? 0); ?></div>
                    <div class="stat-change text-muted">Across <?php echo e(($myCourses ?? collect())->count()); ?> courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-messages" style="color:var(--teal-400)" aria-hidden="true"></i>
                        Threads this week
                    </div>
                    <div class="stat-value"><?php echo e($threadsThisWeek ?? 0); ?></div>
                    <?php $threadDelta = ($threadsThisWeek ?? 0) - ($threadsLastWeek ?? 0); ?>
                    <div class="stat-change <?php echo e($threadDelta >= 0 ? 'text-pos' : 'text-neg'); ?>">
                        <?php echo e($threadDelta >= 0 ? '↑' : '↓'); ?> <?php echo e(abs($threadDelta)); ?> vs last week
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-message-reply" style="color:var(--amber-400)" aria-hidden="true"></i>
                        Unanswered
                    </div>
                    <div class="stat-value"><?php echo e($unansweredCount ?? 0); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($unansweredCount ?? 0) > 0): ?>
                        <div class="stat-change text-neg">Needs response</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-star" style="color:var(--pink-600)" aria-hidden="true"></i>
                        Avg. satisfaction
                    </div>
                    <div class="stat-value"><?php echo e(number_format($avgSatisfaction ?? 0, 1)); ?></div>
                    <?php $satDelta = ($avgSatisfaction ?? 0) - ($prevSatisfaction ?? 0); ?>
                    <div class="stat-change <?php echo e($satDelta >= 0 ? 'text-pos' : 'text-neg'); ?>">
                        <?php echo e($satDelta >= 0 ? '↑' : '↓'); ?> <?php echo e(number_format(abs($satDelta), 1)); ?> this month
                    </div>
                </div>
            </div>

            
            <div class="panel-grid">

                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Unanswered questions</span>
                        <a href="<?php echo e(route('lecturer.discussions.index')); ?>" class="panel-action">Reply to all</a>
                    </div>
                    <div class="panel-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $unansweredDiscussions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $disc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('lecturer.discussions.show', $disc->id)); ?>"
                               style="text-decoration:none;display:block">
                                <div class="disc-item">
                                    <div class="disc-avatar" style="background:var(--purple-50);color:var(--purple-800)">
                                        <?php echo e(strtoupper(substr($disc->user->first_name ?? 'U', 0, 1))); ?><?php echo e(strtoupper(substr($disc->user->last_name ?? '', 0, 1))); ?>

                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div class="disc-title"><?php echo e($disc->title); ?></div>
                                        <div class="disc-meta">
                                            <span class="badge badge-blue"><?php echo e($disc->course->code ?? 'N/A'); ?></span>
                                            Asked <?php echo e($disc->created_at->diffForHumans()); ?>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">
                                No unanswered questions — well done!
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Student engagement</span>
                        <a href="<?php echo e(route('lecturer.engagement')); ?>" class="panel-action">Details</a>
                    </div>
                    <div class="panel-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $courseEngagement ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="progress-wrap">
                                <div class="progress-label">
                                    <span><?php echo e($item['course_code']); ?> — <?php echo e($item['course_name']); ?></span>
                                    <span><?php echo e($item['pct']); ?>%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                         style="width:<?php echo e($item['pct']); ?>%;background:<?php echo e($item['color'] ?? 'var(--purple-600)'); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No engagement data yet.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($overallResponseRate)): ?>
                            <div class="progress-wrap" style="margin-top:8px;padding-top:8px;border-top:var(--border)">
                                <div class="progress-label">
                                    <span style="font-weight:500">Overall response rate</span>
                                    <span><?php echo e($overallResponseRate); ?>%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                         style="width:<?php echo e($overallResponseRate); ?>%;background:var(--pink-600)">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>

            
            <div class="full-panel">
                <div class="panel-head">
                    <span class="panel-title">Discussion threads</span>
                    <a href="<?php echo e(route('lecturer.discussions.create')); ?>" class="panel-action">+ Create thread</a>
                </div>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Course</th>
                                <th>Students engaged</th>
                                <th>Replies</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $threads ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('lecturer.discussions.show', $thread->id)); ?>"
                                           style="color:var(--text);text-decoration:none;font-weight:500">
                                            <?php echo e($thread->title); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo e($thread->badge_color ?? 'blue'); ?>">
                                            <?php echo e($thread->course->code ?? 'N/A'); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($thread->engaged_students ?? 0); ?>/<?php echo e($thread->total_students ?? 0); ?></td>
                                    <td><?php echo e($thread->replies_count ?? 0); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thread->status === 'open'): ?>
                                            <span class="badge badge-green">Open</span>
                                        <?php elseif($thread->status === 'closed'): ?>
                                            <span class="badge badge-amber">Closed</span>
                                        <?php else: ?>
                                            <span class="badge badge-red">Archived</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('lecturer.discussions.edit', $thread->id)); ?>"
                                           style="font-size:12px;color:var(--purple-600);text-decoration:none;">Edit</a>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:1.5rem">No threads yet. Create one to get started.</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($threads) && $threads->hasPages()): ?>
                    <div style="padding:12px 1rem;border-top:var(--border)">
                        <?php echo e($threads->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/lecturer/dashboard.blade.php ENDPATH**/ ?>