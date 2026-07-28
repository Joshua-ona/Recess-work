@extends('layouts.app')

@section('title', 'Attempt Quiz')

@push('styles')
<style>
    /* ============================================
       QUIZ ATTEMPT PAGE STYLES
       ============================================ */
    
    /* Top Bar */
    .quiz-topbar {
        background: linear-gradient(135deg, #6d28d9, #4c1d95);
        color: #fff;
        border-radius: 16px;
        padding: 1.2rem 1.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(109, 40, 217, 0.3);
    }

    .quiz-topbar-title {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.3px;
    }

    .quiz-topbar-sub {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 2px;
    }

    .quiz-exam-notice {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        color: #fbbf24;
        background: rgba(251, 191, 36, 0.15);
        border: 1px solid rgba(251, 191, 36, 0.3);
        border-radius: 999px;
        padding: 4px 14px;
        width: fit-content;
    }

    /* Timer */
    .quiz-timer {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 10px 20px;
        flex-shrink: 0;
        min-width: 140px;
        justify-content: center;
    }

    .quiz-timer i {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.7);
    }

    .quiz-timer-value {
        font-size: 22px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.5px;
    }

    .quiz-timer.is-low {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.4);
        animation: pulse-timer 1s ease-in-out infinite;
    }

    .quiz-timer.is-low .quiz-timer-value,
    .quiz-timer.is-low i {
        color: #f87171;
    }

    @keyframes pulse-timer {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Progress Panel */
    .quiz-progress-panel {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .quiz-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: var(--gray-600);
        margin-bottom: 8px;
    }

    .quiz-progress-label b {
        color: var(--gray-900);
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--gray-200);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.8s ease;
        background: linear-gradient(90deg, #7c3aed, #6d28d9);
    }

    /* Question Card */
    .quiz-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 1.8rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        transition: box-shadow 0.2s;
    }

    .quiz-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .quiz-question-tag {
        display: inline-block;
        font-size: 12px;
        font-weight: 600;
        color: #7c3aed;
        background: #ede9fe;
        border-radius: 999px;
        padding: 4px 14px;
        margin-bottom: 12px;
    }

    .quiz-question-text {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }

    /* Options */
    .quiz-option {
        display: flex;
        align-items: center;
        gap: 14px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quiz-option:hover {
        border-color: #7c3aed;
        background: #f5f3ff;
        transform: translateX(4px);
    }

    .quiz-option input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: #7c3aed;
        cursor: pointer;
        flex-shrink: 0;
    }

    .quiz-option label {
        font-size: 15px;
        color: var(--gray-700);
        cursor: pointer;
        flex: 1;
        font-weight: 500;
    }

    .quiz-option.is-selected {
        border-color: #7c3aed;
        background: #ede9fe;
        transform: translateX(4px);
    }

    .quiz-option.is-selected label {
        font-weight: 600;
        color: #5b21b6;
    }

    /* Actions */
    .quiz-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .quiz-actions .btn {
        min-width: 140px;
        justify-content: center;
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #7c3aed;
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #6d28d9;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(124, 58, 237, 0.35);
    }

    .btn-outline {
        background: transparent;
        color: var(--gray-700);
        border: 2px solid var(--gray-300);
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline:hover {
        border-color: #7c3aed;
        color: #7c3aed;
        background: #f5f3ff;
    }

    .btn-disabled {
        background: var(--gray-100);
        color: var(--gray-400);
        border: 2px solid var(--gray-200);
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .quiz-topbar {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem;
        }

        .quiz-timer {
            align-self: flex-start;
            min-width: 120px;
            padding: 8px 16px;
        }

        .quiz-timer-value {
            font-size: 18px;
        }

        .quiz-card {
            padding: 1.25rem;
        }

        .quiz-question-text {
            font-size: 16px;
        }

        .quiz-option {
            padding: 12px 14px;
        }

        .quiz-option label {
            font-size: 14px;
        }

        .quiz-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .quiz-actions .btn {
            min-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .quiz-topbar-title {
            font-size: 17px;
        }

        .quiz-timer-value {
            font-size: 16px;
        }

        .quiz-progress-label {
            font-size: 12px;
            flex-direction: column;
            gap: 4px;
        }
    }
</style>
@endpush

@section('body')
<div class="dash-wrap">

    @include('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ])

    <div class="dash-main">
        <div class="dash-body">

            {{-- Top Bar --}}
            <div class="quiz-topbar">
                <div>
                    <div class="quiz-topbar-title">{{ $quiz->title ?? 'Quiz' }}</div>
                    <div class="quiz-topbar-sub">{{ $quiz->course->course_name ?? '' }}</div>
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

            {{-- Progress Panel --}}
            <div class="quiz-progress-panel">
                <div class="quiz-progress-label">
                    <span>Page <b>{{ $currentPage ?? 1 }}</b> of <b>{{ $totalPages ?? 1 }}</b></span>
                    <span>
                        {{ round((($currentPage ?? 1) / max(1, $totalPages ?? 1)) * 100) }}% complete
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" 
                         style="width: {{ round((($currentPage ?? 1) / max(1, $totalPages ?? 1)) * 100) }}%;">
                    </div>
                </div>
            </div>

            {{-- Quiz Form --}}
            <form method="POST" action="{{ route('student.quizzes.answer', $quiz->quiz_id ?? 0) }}" id="quiz-form">
                @csrf

                <input type="hidden" id="auto_submitted" name="auto_submitted" value="0">

                @forelse($questions ?? [] as $qIndex => $question)
                    <div class="quiz-card">
                        <span class="quiz-question-tag">
                            Question {{ (($currentPage ?? 1) - 1) * 5 + $qIndex + 1 }}
                        </span>
                        <div class="quiz-question-text">{{ $question->question ?? 'No question text' }}</div>

                        @forelse($question->options ?? [] as $option)
                            @php
                                $savedAnswers = $savedAnswers ?? [];
                                $isChecked = isset($savedAnswers[$question->question_id ?? 0])
                                    && (string) $savedAnswers[$question->question_id ?? 0] === (string) $option->id;
                            @endphp
                            <div class="quiz-option @if($isChecked) is-selected @endif" data-quiz-option>
                                <input
                                    class="quiz-option-input"
                                    type="radio"
                                    name="answers[{{ $question->question_id ?? 0 }}]"
                                    value="{{ $option->id ?? 0 }}"
                                    id="option{{ $option->id ?? 0 }}"
                                    @checked($isChecked)>

                                <label for="option{{ $option->id ?? 0 }}">
                                    {{ $option->option_text ?? 'No option text' }}
                                </label>
                            </div>
                        @empty
                            <p style="color: var(--gray-500); font-size: 14px;">No options available for this question.</p>
                        @endforelse
                    </div>
                @empty
                    <div class="quiz-card" style="text-align: center; padding: 3rem;">
                        <i class="ti ti-clipboard-list" style="font-size: 48px; color: var(--gray-300);"></i>
                        <h3 style="margin-top: 1rem; color: var(--gray-700);">No questions found</h3>
                        <p style="color: var(--gray-500);">This quiz doesn't have any questions yet.</p>
                    </div>
                @endforelse

                <input type="hidden" name="next" value="{{ ($currentPage ?? 1) + 1 }}">

                <div class="quiz-actions">
                    @if($previousPage ?? false)
                        <a href="{{ route('student.quizzes.attempt', [$quiz->quiz_id ?? 0, $previousPage ?? 1]) }}"
                           class="btn btn-outline">
                            <i class="ti ti-arrow-left" aria-hidden="true"></i> 
                            Previous
                        </a>
                    @else
                        <button class="btn btn-disabled" disabled>
                            <i class="ti ti-arrow-left" aria-hidden="true"></i> 
                            Previous
                        </button>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        {{ ($currentPage ?? 1) >= ($totalPages ?? 1) ? 'Submit Quiz' : 'Save & Next' }}
                        <i class="ti ti-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // QUIZ OPTION SELECTION
    // ============================================================
    document.querySelectorAll('[data-quiz-option]').forEach(function(box) {
        var input = box.querySelector('input[type="radio"]');
        if (input) {
            input.addEventListener('change', function() {
                var name = input.getAttribute('name');
                document.querySelectorAll('input[name="' + name + '"]').forEach(function(sibling) {
                    var siblingBox = sibling.closest('[data-quiz-option]');
                    if (siblingBox) {
                        siblingBox.classList.remove('is-selected');
                    }
                });
                box.classList.add('is-selected');
                saveCurrentAnswers();
            });
        }
    });

    // ============================================================
    // VARIABLES
    // ============================================================
    var seconds = {{ (int) ($remainingSeconds ?? 0) }};
    var timerEl = document.getElementById('timer');
    var timerBox = document.getElementById('quiz-timer');
    var form = document.getElementById('quiz-form');
    var submitted = false;
    var warned10 = false;
    var warned7 = false;
    var timerInterval = null;
    var autoRefreshInterval = null;
    var quizId = {{ $quiz->quiz_id ?? 0 }};
    var totalPages = {{ $totalPages ?? 1 }};
    var currentPage = {{ $currentPage ?? 1 }};
    var navigatingAway = false;

    // ============================================================
    // TIMER
    // ============================================================
    function render() {
        var s = Math.max(0, seconds);
        var hrs = Math.floor(s / 3600);
        var mins = Math.floor((s % 3600) / 60);
        var secs = s % 60;

        if (timerEl) {
            timerEl.textContent =
                String(hrs).padStart(2, '0') + ':' +
                String(mins).padStart(2, '0') + ':' +
                String(secs).padStart(2, '0');
        }

        if (timerBox) {
            timerBox.classList.toggle('is-low', s <= 300);
        }
    }

    if (seconds > 0) {
        render();

        timerInterval = setInterval(function() {
            seconds--;
            render();

            if (seconds <= 600 && !warned10) {
                warned10 = true;
                alert("⚠ Warning! Only 10 minutes left.");
            }

            if (seconds <= 420 && !warned7) {
                warned7 = true;
                alert("⚠ Final Warning! Only 7 minutes left.");
            }

            if (seconds <= 0 && !submitted) {
                submitted = true;
                clearInterval(timerInterval);
                timerInterval = null;
                console.log("⏰ Time's up - auto-submitting quiz");
                submitAllAnswers();
            }
        }, 1000);
    }

    // ============================================================
    // AUTOSAVE (30 seconds) — silent, no reload
    // ============================================================
    if (!submitted) {
        autoRefreshInterval = setInterval(function() {
            if (submitted) return;
            console.log("💾 Autosaving answers (30s interval, no reload)");
            saveCurrentAnswers();
        }, 30000);
    }

    // ============================================================
    // SAVE FUNCTIONS
    // ============================================================
    function saveCurrentAnswers() {
        var currentAnswers = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(input) {
            var name = input.getAttribute('name');
            var match = name ? name.match(/answers\[(\d+)\]/) : null;
            if (match) {
                currentAnswers[match[1]] = input.value;
            }
        });

        var savedData = localStorage.getItem('quiz_answers_' + quizId);
        if (savedData) {
            try {
                var parsed = JSON.parse(savedData);
                Object.assign(currentAnswers, parsed);
            } catch(e) {
                console.error("Error parsing saved answers:", e);
            }
        }

        localStorage.setItem('quiz_answers_' + quizId, JSON.stringify(currentAnswers));
        console.log("💾 Current answers saved:", currentAnswers);
    }

    function saveAllAnswers() {
        saveCurrentAnswers();
        console.log("💾 All answers saved to localStorage");
    }

    // ============================================================
    // SUBMIT FUNCTIONS
    // ============================================================
    function submitAllAnswers() {
        console.log("📨 submitAllAnswers() called");

        if (submitted) {
            console.log("⛔ Quiz already submitted, skipping");
            return;
        }

        submitted = true;
        console.log("✅ Quiz marked as submitted");

        var allAnswers = {};
        var savedData = localStorage.getItem('quiz_answers_' + quizId);
        if (savedData) {
            try {
                allAnswers = JSON.parse(savedData);
                console.log("📦 Retrieved all answers from localStorage:", allAnswers);
            } catch(e) {
                console.error("Error parsing saved answers:", e);
            }
        }

        document.querySelectorAll('input[type="radio"]:checked').forEach(function(input) {
            var name = input.getAttribute('name');
            var match = name ? name.match(/answers\[(\d+)\]/) : null;
            if (match) {
                allAnswers[match[1]] = input.value;
            }
        });

        if (Object.keys(allAnswers).length === 0) {
            console.warn("⚠️ No answers found to submit");
        }

        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
            console.log("✅ Timer cleared");
        }
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            console.log("✅ Auto-refresh cleared");
        }

        submitViaAjax(allAnswers);
    }

    function submitViaAjax(allAnswers) {
        var token = document.querySelector('input[name="_token"]');
        if (!token) {
            console.error("❌ CSRF token not found");
            submitViaForm(allAnswers);
            return;
        }

        var formData = new FormData();
        formData.append('_token', token.value);
        formData.append('all_answers', JSON.stringify(allAnswers));
        formData.append('auto_submitted', '1');
        formData.append('quiz_id', quizId);
        formData.append('current_page', currentPage);
        formData.append('total_pages', totalPages);

        console.log("📤 Submitting ALL answers via AJAX...");

        var submitUrl = '{{ route("student.quizzes.submit-all", $quiz->quiz_id ?? 0) }}';
        
        fetch(submitUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function(data) {
            console.log("✅ Quiz submitted successfully:", data);
            localStorage.removeItem('quiz_answers_' + quizId);

            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = '{{ route("student.quizzes.results", $quiz->quiz_id ?? 0) }}';
            }
        })
        .catch(function(error) {
            console.error("❌ Error submitting quiz:", error);
            alert("Error submitting quiz. Please try again or contact support.");
            submitted = false;
            submitViaForm(allAnswers);
        });
    }

    function submitViaForm(allAnswers) {
        console.log("📤 Submitting ALL answers via form...");

        var autoInput = document.getElementById('auto_submitted');
        if (autoInput) {
            autoInput.value = 1;
        }

        document.querySelectorAll('input[name^="answers["]').forEach(function(input) {
            if (input.type === 'radio') {
                input.remove();
            }
        });

        for (var questionId in allAnswers) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'answers[' + questionId + ']';
            input.value = allAnswers[questionId];
            if (form) {
                form.appendChild(input);
            }
        }

        var allPagesInput = document.createElement('input');
        allPagesInput.type = 'hidden';
        allPagesInput.name = 'submit_all_pages';
        allPagesInput.value = '1';
        if (form) {
            form.appendChild(allPagesInput);
        }

        var nextInput = document.querySelector('input[name="next"]');
        if (nextInput) {
            nextInput.value = '0';
        }

        if (form) {
            form.submit();
            console.log("✅ Form submitted successfully");
        }
    }

    // ============================================================
    // RESTORE ANSWERS ON PAGE LOAD
    // ============================================================
    function restoreAnswers() {
        var savedData = localStorage.getItem('quiz_answers_' + quizId);
        if (savedData) {
            try {
                var answers = JSON.parse(savedData);
                console.log("🔄 Restoring saved answers:", answers);

                document.querySelectorAll('input[type="radio"]').forEach(function(input) {
                    var name = input.getAttribute('name');
                    var match = name ? name.match(/answers\[(\d+)\]/) : null;
                    if (match) {
                        var questionId = match[1];
                        if (answers[questionId] && input.value == answers[questionId]) {
                            input.checked = true;
                            var optionBox = input.closest('[data-quiz-option]');
                            if (optionBox) {
                                optionBox.classList.add('is-selected');
                            }
                        }
                    }
                });
                console.log("✅ Answers restored successfully");
            } catch(e) {
                console.error("Error restoring answers:", e);
            }
        }
    }

    restoreAnswers();

    // ============================================================
    // LEAVE-PREVENTION
    // ============================================================
    window.addEventListener('beforeunload', function(e) {
        saveCurrentAnswers();
        if (!submitted && !navigatingAway) {
            var message = 'You have an unfinished quiz. Are you sure you want to leave?';
            e.preventDefault();
            e.returnValue = message;
            return message;
        }
    });

    // ============================================================
    // TAB SWITCH DETECTION
    // ============================================================
    document.addEventListener('visibilitychange', function() {
        console.log("🔄 Visibility change detected");
        if (navigatingAway) {
            console.log("↪️ In-app navigation — not a tab switch, skipping auto-submit");
            return;
        }
        if (document.hidden && !submitted) {
            console.log("🔴 Tab switch detected - saving and submitting all answers");
            saveAllAnswers();
            submitAllAnswers();
        } else {
            console.log("👁️ User returned to tab");
        }
    });

    // ============================================================
    // WINDOW BLUR
    // ============================================================
    window.addEventListener('blur', function() {
        if (!submitted) {
            console.log("🔴 Window lost focus - saving answers");
            saveCurrentAnswers();
        }
    });

    // ============================================================
    // INTERCEPT PAGINATION LINKS
    // ============================================================
    document.querySelectorAll('.quiz-actions a, .quiz-actions button').forEach(function(el) {
        if (el.closest('.quiz-actions')) {
            el.addEventListener('click', function(e) {
                navigatingAway = true;
                saveCurrentAnswers();
            });
        }
    });

    console.log("✅ Quiz script loaded successfully");
    console.log("💡 Timer started:", seconds, "seconds remaining");
    console.log("💡 Answers will be saved to localStorage");
    console.log("💡 Tab switching will auto-submit ALL answers");
    console.log("💡 Auto-refresh every 30 seconds (answers preserved)");
    console.log("💡 Leave prevention is active");

    // ============================================================
    // EXAM LOCKDOWN — disable navigating elsewhere during the attempt
    // ============================================================
    document.querySelectorAll('.sidebar-item, .mobile-nav-item').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (submitted) return; // quiz is done, let them navigate freely
            e.preventDefault();
            alert('You cannot leave this page while the quiz is in progress.');
        });
        link.style.pointerEvents = 'none';
        link.style.opacity = '0.45';
    });

    // Block right-click / context menu (copy protection — best effort only)
    document.addEventListener('contextmenu', function (e) {
        if (!submitted) e.preventDefault();
    });

    // Block copy / cut and common devtools shortcuts (best effort only —
    // determined users can always bypass client-side restrictions)
    document.addEventListener('keydown', function (e) {
        if (submitted) return;
        var blockedKey =
            (e.ctrlKey && ['c', 'x', 'u', 'p'].includes(e.key.toLowerCase())) ||
            e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key));
        if (blockedKey) {
            e.preventDefault();
        }
    });
    document.addEventListener('copy', function (e) {
        if (!submitted) e.preventDefault();
    });

    // Trap the browser back button — pushes a new history entry so the user
    // can't navigate away with back/forward while the attempt is in progress
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function () {
        if (!submitted) {
            history.pushState(null, '', location.href);
            alert('You cannot go back while the quiz is in progress.');
        }
    });
});
</script>
@endpush

@endsection