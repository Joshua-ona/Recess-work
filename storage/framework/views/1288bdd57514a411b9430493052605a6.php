

<?php $__env->startSection('title','Quiz Result'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .result-wrap {
        max-width: 560px;
        margin: 2rem auto;
    }

    .result-card {
        background: var(--surface);
        border: var(--border);
        border-radius: var(--radius-xl);
        padding: 2.5rem 2rem;
        text-align: center;
    }

    .result-ring {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        margin: 0 auto 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background: conic-gradient(var(--purple-600) calc(var(--pct) * 1%), var(--purple-50) 0);
    }

    .result-ring-inner {
        width: 112px;
        height: 112px;
        border-radius: 50%;
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .result-pct {
        font-size: 26px;
        font-weight: 700;
        color: var(--text);
    }

    .result-pct-label {
        font-size: 11px;
        color: var(--muted);
    }

    .result-title {
        font-size: 19px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .result-sub {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 1.5rem;
    }

    .result-stats {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 1.75rem;
    }

    .result-stat {
        border: var(--border);
        border-radius: var(--radius-md);
        padding: 12px 20px;
        min-width: 100px;
    }

    .result-stat-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
    }

    .result-stat-label {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    .result-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .result-actions .btn {
        width: auto;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>

<div class="dash-wrap">

    <?php echo $__env->make('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dash-main">
        <div class="dash-body">

            <div class="result-wrap">
                <div class="result-card">

                    <?php
                        $pct = $total > 0 ? round(($score / $total) * 100) : 0;
                    ?>

                    <div class="result-ring" style="--pct: <?php echo e($pct); ?>;">
                        <div class="result-ring-inner">
                            <div class="result-pct"><?php echo e($pct); ?>%</div>
                            <div class="result-pct-label">Score</div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($timedOut ?? false): ?>
                        <span class="badge badge-amber" style="margin-bottom:10px;">Time expired</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="result-title"><?php echo e($quiz->title); ?></div>
                    <div class="result-sub">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($timedOut ?? false): ?>
                            Time ran out — here's how you did with the answers you submitted.
                        <?php else: ?>
                            Nice work, you've completed the quiz!
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="result-stats">
                        <div class="result-stat">
                            <div class="result-stat-value"><?php echo e($score); ?></div>
                            <div class="result-stat-label">Correct</div>
                        </div>
                        <div class="result-stat">
                            <div class="result-stat-value"><?php echo e($total); ?></div>
                            <div class="result-stat-label">Total questions</div>
                        </div>
                    </div>

                    <div class="result-actions">
                        <a href="<?php echo e(route('student.quizzes')); ?>" class="btn btn-outline">
                            <i class="ti ti-list" aria-hidden="true"></i> Back to quizzes
                        </a>
                        <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-primary">
                            <i class="ti ti-home" aria-hidden="true"></i> Dashboard
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/student/quizzes/result.blade.php ENDPATH**/ ?>