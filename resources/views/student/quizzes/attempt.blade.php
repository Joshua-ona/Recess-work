@extends('layouts.app')

@section('title','Attempt Quiz')

@push('styles')
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
@endpush

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar',[
        'role'=>'student',
        'user'=>auth()->user()
    ])

    <div class="dash-main">
        <div class="dash-body">

            <div class="quiz-topbar">
                <div>
                    <div class="quiz-topbar-title">{{ $quiz->title }}</div>
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

            <div class="quiz-progress-panel">
                <div class="quiz-progress-label">
                    <span>Page <b>{{ $currentPage }}</b> of <b>{{ $totalPages }}</b></span>
                    <span>{{ round(($currentPage / max(1, $totalPages)) * 100) }}% complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:{{ ($currentPage / max(1, $totalPages)) * 100 }}%; background:var(--purple-600);"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('student.quizzes.answer', $quiz->quiz_id) }}" id="quiz-form">
                @csrf

<input type="hidden"
       id="auto_submitted"
       name="auto_submitted"
       value="0">

                @foreach($questions as $qIndex => $question)
                    <div class="quiz-card">
                        <span class="quiz-question-tag">Question {{ (($currentPage - 1) * 5) + $qIndex + 1 }}</span>
                        <div class="quiz-question-text">{{ $question->question }}</div>

                        @foreach($question->options as $option)
                            @php
                                $savedAnswers = $savedAnswers ?? [];
                                $isChecked = isset($savedAnswers[$question->question_id])
                                    && (string) $savedAnswers[$question->question_id] === (string) $option->id;
                            @endphp
                            <div class="quiz-option @if($isChecked) is-selected @endif" data-quiz-option>
                                <input
                                    class="quiz-option-input"
                                    type="radio"
                                    name="answers[{{ $question->question_id }}]"
                                    value="{{ $option->id }}"
                                    id="option{{ $option->id }}"
                                    @checked($isChecked)>

                                <label for="option{{ $option->id }}">
                                    {{ $option->option_text }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <input type="hidden" name="next" value="{{ $currentPage + 1 }}">

                <div class="quiz-actions">
                    @if($previousPage)
                        <a
                           href="{{ route('student.quizzes.attempt',[$quiz->quiz_id,$previousPage]) }}"
                           class="btn btn-outline">
                            <i class="ti ti-arrow-left" aria-hidden="true"></i> Previous
                        </a>
                    @else
                        <button class="btn btn-disabled" disabled>
                            <i class="ti ti-arrow-left" aria-hidden="true"></i> Previous
                        </button>
                    @endif

                    <button class="btn btn-primary" style="width:auto;">
                        {{ $currentPage >= $totalPages ? 'Submit Quiz' : 'Save & Next' }}
                        <i class="ti ti-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
   
    document.querySelectorAll('[data-quiz-option]').forEach(function (box) {
        var input = box.querySelector('input[type="radio"]');
        input.addEventListener('change', function () {
            var name = input.getAttribute('name');
            document.querySelectorAll('input[name="' + name + '"]').forEach(function (sibling) {
                sibling.closest('[data-quiz-option]').classList.remove('is-selected');
            });
            box.classList.add('is-selected');
            
            // Save answer immediately when selected
            saveCurrentAnswers();
        });
    });

    var seconds = {{ (int) $remainingSeconds }};
    var timerEl = document.getElementById('timer');
    var timerBox = document.getElementById('quiz-timer');
    var form = document.getElementById('quiz-form');
    var submitted = false;
    var warned10 = false;
    var warned7 = false;
    var timerInterval = null;
    var quizId = {{ $quiz->quiz_id }};
    var totalPages = {{ $totalPages }};
    var currentPage = {{ $currentPage }};
    var navigatingAway = false;

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

    timerInterval = setInterval(function () {
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

    // Function to save current page answers
    function saveCurrentAnswers() {
        var currentAnswers = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(input) {
            var name = input.getAttribute('name');
            var match = name.match(/answers\[(\d+)\]/);
            if (match) {
                currentAnswers[match[1]] = input.value;
            }
        });
        
        // Merge with existing saved answers
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

    // Function to save ALL answers (current page + localStorage)
    function saveAllAnswers() {
        saveCurrentAnswers(); // This already merges with localStorage
        console.log("💾 All answers saved to localStorage");
    }

    // Function to submit ALL answers
    function submitAllAnswers() {
        console.log("📨 submitAllAnswers() called");
        
        if (submitted) {
            console.log("⛔ Quiz already submitted, skipping");
            return;
        }

        submitted = true;
        console.log("✅ Quiz marked as submitted");

        // Get all answers from localStorage
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

        // Also get current page answers (in case localStorage failed)
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(input) {
            var name = input.getAttribute('name');
            var match = name.match(/answers\[(\d+)\]/);
            if (match) {
                allAnswers[match[1]] = input.value;
            }
        });

        // Check if we have any answers
        if (Object.keys(allAnswers).length === 0) {
            console.warn("⚠️ No answers found to submit");
            // Still submit empty quiz
        }

        // Clear timer
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
            console.log("✅ Timer cleared");
        }

        // Method 1: Submit via AJAX (Recommended)
        submitViaAjax(allAnswers);
        
        // OR Method 2: Submit via form with hidden inputs
        // submitViaForm(allAnswers);
    }

    // Method 1: AJAX Submission (Recommended)
    function submitViaAjax(allAnswers) {
        var formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('all_answers', JSON.stringify(allAnswers));
        formData.append('auto_submitted', '1');
        formData.append('quiz_id', quizId);
        formData.append('current_page', currentPage);
        formData.append('total_pages', totalPages);

        console.log("📤 Submitting ALL answers via AJAX...");

        fetch('{{ route("student.quizzes.submit-all", $quiz->quiz_id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log("✅ Quiz submitted successfully:", data);
            // Clear localStorage after successful submission
            localStorage.removeItem('quiz_answers_' + quizId);
            
            // Redirect to results page
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = '{{ route("student.quizzes.results", $quiz->quiz_id) }}';
            }
        })
        .catch(error => {
            console.error("❌ Error submitting quiz:", error);
            alert("Error submitting quiz. Please try again or contact support.");
            submitted = false; // Allow retry
            
            // Fallback: Submit via form
            submitViaForm(allAnswers);
        });
    }

    // Method 2: Form Submission with hidden inputs
    function submitViaForm(allAnswers) {
        console.log("📤 Submitting ALL answers via form...");

        // Set auto_submitted flag
        var autoInput = document.getElementById('auto_submitted');
        if (autoInput) {
            autoInput.value = 1;
        }

        // Remove existing answer inputs (to avoid duplicates)
        document.querySelectorAll('input[name^="answers["]').forEach(function(input) {
            if (input.type === 'radio') {
                input.remove();
            }
        });

        // Add all answers as hidden inputs
        for (var questionId in allAnswers) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'answers[' + questionId + ']';
            input.value = allAnswers[questionId];
            form.appendChild(input);
        }

        // Add submit_all_pages flag
        var allPagesInput = document.createElement('input');
        allPagesInput.type = 'hidden';
        allPagesInput.name = 'submit_all_pages';
        allPagesInput.value = '1';
        form.appendChild(allPagesInput);

        // Change the next value to indicate completion
        var nextInput = document.querySelector('input[name="next"]');
        if (nextInput) {
            nextInput.value = '0';
        }

        // Submit the form
        form.submit();
        console.log("✅ Form submitted successfully");
    }

    // Restore answers when page loads
    function restoreAnswers() {
        var savedData = localStorage.getItem('quiz_answers_' + quizId);
        if (savedData) {
            try {
                var answers = JSON.parse(savedData);
                console.log("🔄 Restoring saved answers:", answers);
                
                document.querySelectorAll('input[type="radio"]').forEach(function(input) {
                    var name = input.getAttribute('name');
                    var match = name.match(/answers\[(\d+)\]/);
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

    // Call restore on page load
    restoreAnswers();

    // Save answers when leaving page (for pagination navigation)
    window.addEventListener('beforeunload', function() {
        saveCurrentAnswers();
    });

    // Intercept pagination links to save answers
    document.querySelectorAll('.quiz-actions a, .quiz-actions button').forEach(function(el) {
        if (el.closest('.quiz-actions')) {
            el.addEventListener('click', function(e) {
                // This is a legitimate in-app navigation (Previous / Save & Next),
                // not a tab-switch — don't let visibilitychange treat it as cheating.
                navigatingAway = true;
                // Save answers before navigating
                saveCurrentAnswers();
            });
        }
    });

    // TAB SWITCH DETECTION - SUBMIT ALL ANSWERS
    document.addEventListener('visibilitychange', function () {
        console.log("🔄 Visibility change detected");
        if (navigatingAway) {
            console.log("↪️ In-app navigation (Previous/Next/Submit) — not a tab switch, skipping auto-submit");
            return;
        }
        if (document.hidden && !submitted) {
            console.log("🔴 Tab switch detected - saving and submitting all answers");
            // Save all answers first
            saveAllAnswers();
            // Submit all answers
            submitAllAnswers();
        } else {
            console.log("👁️ User returned to tab");
        }
    });

    // Window blur as additional safety
    window.addEventListener('blur', function() {
        if (!submitted) {
            console.log("🔴 Window lost focus - saving answers");
            saveCurrentAnswers();
        }
    });

    // Also submit if user tries to close the tab
    window.addEventListener('beforeunload', function(e) {
        if (!submitted) {
            saveAllAnswers();
            // Note: Can't prevent tab close, but we save answers
        }
    });

    console.log("✅ Quiz script loaded successfully");
    console.log("💡 Timer started:", seconds, "seconds remaining");
    console.log("💡 Answers will be saved to localStorage");
    console.log("💡 Tab switching will auto-submit ALL answers");
    console.log("💡 submitAllAnswers() function is available");

})();
</script>

@endpush

@endsection
