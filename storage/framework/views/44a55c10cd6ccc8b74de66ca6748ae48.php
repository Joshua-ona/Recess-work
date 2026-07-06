<?php $__env->startSection('title', 'Available Quizzes'); ?>

<?php $__env->startSection('body'); ?>

<div class="container mt-4">

    <h2>Available Quizzes</h2>

    <?php if($quizzes->isEmpty()): ?>
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

            <?php $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

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

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE i5\Desktop\RECESS\Recess-work\resources\views/student/quizzes/index.blade.php ENDPATH**/ ?>