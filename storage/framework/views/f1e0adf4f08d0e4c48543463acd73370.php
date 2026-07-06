<?php $__env->startSection('title', 'Available Quizzes'); ?>

<?php $__env->startSection('body'); ?>

<div class="container mt-4">

    <h2>Available Quizzes</h2>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quizzes->isEmpty()): ?>
        <p>No quizzes available.</p>
    <?php else: ?>

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Title</th>
                    <th>Start Time</th>
                    <th>Duration</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

                <tr>

                    <td><?php echo e($quiz->title); ?></td>

                    <td><?php echo e($quiz->start_time); ?></td>

                    <td><?php echo e($quiz->duration_mins); ?> mins</td>

                    <td>
                       <a href="<?php echo e(route('student.quizzes.attempt', $quiz->quiz_id)); ?>"
   class="btn btn-primary">
    Start Quiz
</a>
                    </td>

                </tr>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            </tbody>

        </table>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/student/quizzes/index.blade.php ENDPATH**/ ?>