<?php $__env->startSection('title','Attempt Quiz'); ?>

<?php $__env->startSection('body'); ?>

<div class="dash-wrap">

    <?php echo $__env->make('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dash-main">

        <div class="container-fluid">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h4 class="mb-0"><?php echo e($quiz->title); ?></h4>
                            <small><?php echo e($quiz->course->course_name ?? ''); ?></small>
                            EXAM MODE ACTIVE:
                            LEAVING THIS PAGE OR OPENING ANOTHER TAB WILL TERMINATE YOUR ATTEMPT 
                            IMMEDIATELY!
                        </div>

                        <div>

                            <h5 id="timer" class="mb-0">
                                01:30:00
                            </h5>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="mb-4">

                        <div class="progress">

                            <div class="progress-bar"
                                    style="width:<?php echo e(($currentPage / $totalPages) * 100); ?>%">
                                </div>

                                <small>
                                    Page <?php echo e($currentPage); ?>

                                    of
                                    <?php echo e($totalPages); ?>

                                </small>

                    </div>

                   <form method="POST"
      action="<?php echo e(route('student.quizzes.answer', $quiz->quiz_id)); ?>">
    
                        <?php echo csrf_field(); ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

    <h4 class="mb-4">
        <?php echo e($question->question); ?>

    </h4>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

        <div class="form-check border rounded p-3 mb-3">

            <input
                class="form-check-input"
                type="radio"
                name="answers[<?php echo e($question->question_id); ?>]"
                value="<?php echo e($option->id); ?>"
                id="option<?php echo e($option->id); ?>">

            <label
                class="form-check-label ms-2"
                for="option<?php echo e($option->id); ?>">

                <?php echo e($option->option_text); ?>


            </label>

        </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    <hr>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                        <div class="d-flex justify-content-between mt-5">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previousPage): ?>

                                <a
                                   href="<?php echo e(route('student.quizzes.attempt',[$quiz->id,$previousQuestion])); ?>"
                                   class="btn btn-secondary">

                                    Previous

                                </a>

                            <?php else: ?>
                            <input
                                type="hidden"
                                name="next"
                                value="<?php echo e($currentPage + 1); ?>">

                                <button
                                    class="btn btn-secondary"
                                    disabled>

                                    Previous

                                </button>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <button
                                class="btn btn-warning">

                                Save & Next

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
<script>

let seconds = 5400;

setInterval(function(){

    let hrs=Math.floor(seconds/3600);

    let mins=Math.floor((seconds%3600)/60);

    let secs=seconds%60;

    document.getElementById('timer').innerHTML=

        String(hrs).padStart(2,'0')+":"+

        String(mins).padStart(2,'0')+":"+

        String(secs).padStart(2,'0');

    seconds--;

},1000);

</script>
<style>
    .option-box{

padding:18px;

border:1px solid #ddd;

border-radius:12px;

margin-bottom:15px;

cursor:pointer;

transition:.2s;

}

.option-box:hover{

background:#eef6ff;

border-color:#0d6efd;

}

.progress{

height:10px;

}

.question-nav{

display:flex;

flex-wrap:wrap;

gap:10px;

}

.question-nav button{

width:45px;

height:45px;

font-weight:bold;

border-radius:50%;

}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/student/quizzes/attempt.blade.php ENDPATH**/ ?>