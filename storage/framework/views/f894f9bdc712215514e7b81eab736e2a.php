<?php $__env->startSection('title','Attempt Quiz'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .quiz-topbar {
        background: var(--purple-900);
        color: #fff;
        border-radius: var(--radius-lg);
        padding: 1.1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .quiz-topbar-title {
        font-size: 17px;
        font-weight: 600;
    }

    .quiz-topbar-sub {
        font-size: 12px;
        color: rgba(255, 255, 255, .55);
        margin-top: 2px;
    }

    .quiz-exam-notice {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .3px;
        color: var(--amber-400);
        background: rgba(186, 117, 23, .15);
        border: 1px solid rgba(186, 117, 23, .35);
        border-radius: 999px;
        padding: 4px 10px;
        width: fit-content;
    }

    .quiz-timer {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: var(--radius-md);
        padding: 8px 16px;
        flex-shrink: 0;
    }

    .quiz-timer i {
        font-size: 18px;
        color: rgba(255, 255, 255, .7);
    }

    .quiz-timer-value {
        font-size: 20px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        letter-spacing: .5px;
    }

    .quiz-timer.is-low {
        background: rgba(226, 75, 74, .18);
        border-color: rgba(226, 75, 74, .4);
    }

    .quiz-timer.is-low .quiz-timer-value,
    .quiz-timer.is-low i {
        color: var(--red-400);
    }

    .quiz-progress-panel {
        background: var(--surface);
        border: var(--border);
        border-radius: var(--radius-lg);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }

    .quiz-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .quiz-progress-label b {
        color: var(--text);
    }

    .quiz-card {
        background: var(--surface);
        border: var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .quiz-question-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        color: var(--purple-600);
        background: var(--purple-50);
        border-radius: 999px;
        padding: 3px 10px;
        margin-bottom: 10px;
    }

    .quiz-question-text {
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 1.1rem;
    }

    .quiz-option {
        display: flex;
        align-items: center;
        gap: 12px;
        border: var(--border-em);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }

    .quiz-option:hover {
        border-color: var(--purple-400);
        background: var(--purple-50);
    }

    .quiz-option input[type="radio"] {
        width: 16px;
        height: 16px;
        accent-color: var(--purple-600);
        cursor: pointer;
        flex-shrink: 0;
    }

    .quiz-option label {
        font-size: 14px;
        color: var(--text);
        cursor: pointer;
        flex: 1;
    }

    .quiz-option.is-selected {
        border-color: var(--purple-600);
        background: var(--purple-50);
    }

    .quiz-option.is-selected label {
        font-weight: 600;
        color: var(--purple-800);
    }

    .quiz-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
    }

    .quiz-actions .btn {
        min-width: 130px;
        justify-content: center;
    }

    .btn-disabled {
        background: var(--bg);
        color: var(--hint);
        border-color: var(--border-em);
        cursor: not-allowed;
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

            <div class="quiz-topbar">
                <div>
                    <div class="quiz-topbar-title"><?php echo e($quiz->title); ?></div>
                    <div class="quiz-topbar-sub"><?php echo e($quiz->course->course_name ?? ''); ?></div>
                    <div class="quiz-exam-notice">
                        <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                        Exam mode — leaving this page or opening another tab ends your attempt
                    </div>
                </div>

                <div class="quiz-timer" id="quiz-timer">
                    <i class="ti ti-clock" aria-hidden="true"></i>
                    <span class="quiz-timer-value" id="timer">--:--:--</span>
                </div>
            </div>

            <div class="quiz-progress-panel">
                <div class="quiz-progress-label">
                    <span>Page <b><?php echo e($currentPage); ?></b> of <b><?php echo e($totalPages); ?></b></span>
                    <span><?php echo e(round(($currentPage / max(1, $totalPages)) * 100)); ?>% complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:<?php echo e(($currentPage / max(1, $totalPages)) * 100); ?>%; background:var(--purple-600);"></div>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('student.quizzes.answer', $quiz->quiz_id)); ?>" id="quiz-form">
                <?php echo csrf_field(); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qIndex => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="quiz-card">
                        <span class="quiz-question-tag">Question <?php echo e((($currentPage - 1) * 5) + $qIndex + 1); ?></span>
                        <div class="quiz-question-text"><?php echo e($question->question); ?></div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $savedAnswers = $savedAnswers ?? [];
                                $isChecked = isset($savedAnswers[$question->question_id])
                                    && (string) $savedAnswers[$question->question_id] === (string) $option->id;
                            ?>
                            <div class="quiz-option <?php if($isChecked): ?> is-selected <?php endif; ?>" data-quiz-option>
                                <input
                                    class="quiz-option-input"
                                    type="radio"
                                    name="answers[<?php echo e($question->question_id); ?>]"
                                    value="<?php echo e($option->id); ?>"
                                    id="option<?php echo e($option->id); ?>"
                                    <?php if($isChecked): echo 'checked'; endif; ?>>

                                <label for="option<?php echo e($option->id); ?>">
                                    <?php echo e($option->option_text); ?>

                                </label>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <input type="hidden" name="next" value="<?php echo e($currentPage + 1); ?>">

                <div class="quiz-actions">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($previousPage): ?>
                        <a
                           href="<?php echo e(route('student.quizzes.attempt',[$quiz->quiz_id,$previousPage])); ?>"
                           class="btn btn-outline">
                            <i class="ti ti-arrow-left" aria-hidden="true"></i> Previous
                        </a>
                    <?php else: ?>
                        <button class="btn btn-disabled" disabled>
                            <i class="ti ti-arrow-left" aria-hidden="true"></i> Previous
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <button class="btn btn-primary" style="width:auto;">
                        <?php echo e($currentPage >= $totalPages ? 'Submit Quiz' : 'Save & Next'); ?>

                        <i class="ti ti-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    // Highlight the selected option in each question card.
    document.querySelectorAll('[data-quiz-option]').forEach(function (box) {
        var input = box.querySelector('input[type="radio"]');
        input.addEventListener('change', function () {
            var name = input.getAttribute('name');
            document.querySelectorAll('input[name="' + name + '"]').forEach(function (sibling) {
                sibling.closest('[data-quiz-option]').classList.remove('is-selected');
            });
            box.classList.add('is-selected');
        });
    });

    // Server tells us exactly how much time is left, so the timer keeps
    // counting down across pages instead of resetting each time.
    var seconds = <?php echo e((int) $remainingSeconds); ?>;
    var timerEl = document.getElementById('timer');
    var timerBox = document.getElementById('quiz-timer');
    var form = document.getElementById('quiz-form');
    var submitted = false;

    function render() {
        var s = Math.max(0, seconds);
        var hrs = Math.floor(s / 3600);
        var mins = Math.floor((s % 3600) / 60);
        var secs = s % 60;

        timerEl.textContent =
            String(hrs).padStart(2, '0') + ':' +
            String(mins).padStart(2, '0') + ':' +
            String(secs).padStart(2, '0');

        timerBox.classList.toggle('is-low', s <= 300);
    }

    render();

    var interval = setInterval(function () {
        seconds--;
        render();

        if (seconds <= 0 && !submitted) {
            submitted = true;
            clearInterval(interval);
            // Time's up — submit whatever is answered on this page.
            // The server re-checks the deadline and finalizes the attempt.
            form.submit();
        }
    }, 1000);
})();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views/student/quizzes/attempt.blade.php ENDPATH**/ ?>