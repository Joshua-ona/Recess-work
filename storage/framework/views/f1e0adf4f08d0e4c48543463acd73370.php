<?php $__env->startSection('title', 'Available Quizzes'); ?>

<?php $__env->startSection('body'); ?>

<div class="dash-wrap">

    <?php echo $__env->make('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dash-main">

        <div class="dash-header">
            <div>
                <div class="dash-header-title">Available quizzes</div>
                <div class="dash-header-sub">
                    <?php echo e($quizzes->count()); ?> <?php echo e(Str::plural('quiz', $quizzes->count())); ?> published
                </div>
            </div>
        </div>

        <div class="dash-body">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quizzes->isEmpty()): ?>
                <div class="full-panel">
                    <div class="panel-body" style="text-align:center; padding:2.5rem 1rem;">
                        <i class="ti ti-clipboard-list" style="font-size:32px;color:var(--hint);" aria-hidden="true"></i>
                        <p style="color:var(--muted); margin-top:10px;">No quizzes available right now.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="full-panel">
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Start time</th>
                                    <th>Duration</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr>
                                        <td style="font-weight:500;"><?php echo e($quiz->title); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($quiz->start_time)->format('M j, Y g:i A')); ?></td>
                                        <td>
                                            <span class="badge badge-purple">
                                                <i class="ti ti-clock" aria-hidden="true"></i>&nbsp;<?php echo e($quiz->duration_mins); ?> mins
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('student.quizzes.attempt', $quiz->quiz_id)); ?>"
                                               class="btn btn-primary btn-sm" style="width:auto;">
                                                Start quiz <i class="ti ti-arrow-right" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/student/quizzes/index.blade.php ENDPATH**/ ?>