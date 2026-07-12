@extends('layouts.app')

@section('title', 'Edit Quiz')

@push('styles')
<style>
    /* ============================================
       MODERN ENHANCEMENTS
    ============================================ */
    
    /* Page Header Enhancement */
    .dash-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem !important;
    }

    .dash-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .dash-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }

    .dash-header > div {
        position: relative;
        z-index: 1;
    }

    .dash-header .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.75rem !important;
    }

    .dash-header h1 {
        color: white !important;
        font-size: 2rem;
        font-weight: 700;
        margin-top: 0.25rem !important;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dash-header h1::before {
        content: '📝';
        font-size: 1.75rem;
    }

    .dash-header p {
        color: rgba(255, 255, 255, 0.85) !important;
        font-size: 0.95rem;
        max-width: 600px;
    }

    /* Panel Enhancement */
    .panel {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .panel:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        border-color: #d1d5db;
    }

    .panel .panel-head {
        padding: 1rem 1.5rem;
        background: #fafafa;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel .panel-head .panel-title {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin: 0;
    }

    .panel .panel-head .panel-title i {
        color: #7c3aed;
        font-size: 1.1rem;
    }

    .panel .panel-body {
        padding: 1.5rem;
    }

    /* Form Elements Enhancement */
    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 0.375rem;
        display: block;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    .form-label .helper {
        font-weight: 400;
        color: #9ca3af;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }

    .form-control {
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.625rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: white;
        color: #111827;
        width: 100%;
    }

    .form-control:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.08);
        outline: none;
    }

    .form-control:hover {
        border-color: #9ca3af;
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    /* Modern Checkbox Styling */
    .form-check {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        cursor: pointer;
    }

    .form-check-input {
        appearance: none;
        width: 44px;
        height: 24px;
        background: #d1d5db;
        border-radius: 12px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
        border: none;
    }

    .form-check-input::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .form-check-input:checked {
        background: #7c3aed;
    }

    .form-check-input:checked::after {
        transform: translateX(20px);
    }

    .form-check-label {
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
        user-select: none;
        font-weight: 500;
    }

    .form-check .form-check-description {
        font-size: 0.75rem;
        color: #9ca3af;
        font-weight: 400;
    }

    /* Question Card Enhancement */
    .question-card {
        border: 1px solid #e5e7eb !important;
        border-radius: 12px !important;
        margin-bottom: 1.25rem !important;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }

    .question-card:hover {
        box-shadow: 0 4px 16px rgba(124, 58, 237, 0.08) !important;
        border-color: #c4b5fd !important;
    }

    .question-card .card-header {
        background: #fafafa !important;
        padding: 0.875rem 1.25rem !important;
        border-bottom: 1px solid #f3f4f6 !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .question-card .card-header .question-number {
        color: #7c3aed;
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .question-card .card-header .question-number .badge {
        background: #ede9fe;
        color: #7c3aed;
        font-size: 0.65rem;
        padding: 0.125rem 0.5rem;
        border-radius: 10px;
        font-weight: 500;
    }

    .question-card .card-body {
        padding: 1.25rem !important;
    }

    .question-card .card-body label {
        font-weight: 500;
        font-size: 0.813rem;
        color: #374151;
        margin-bottom: 0.25rem;
        display: block;
    }

    /* Button Enhancements */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.625rem 1.25rem;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
    }

    .btn-primary {
        background: #7c3aed;
        color: white;
    }

    .btn-primary:hover {
        background: #6d28d9;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        background: transparent;
        color: #7c3aed;
        border: 1.5px solid #7c3aed;
    }

    .btn-outline-primary:hover {
        background: #ede9fe;
        border-color: #6d28d9;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #e5e7eb;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        transform: translateY(-1px);
    }

    .btn-light {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-light:hover {
        background: #e5e7eb;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    /* Empty State Enhancement */
    #emptyQuestions {
        border: 2px dashed #d1d5db !important;
        border-radius: 12px !important;
        padding: 3rem 2rem !important;
        background: #fafafa !important;
        text-align: center;
    }

    #emptyQuestions i {
        color: #7c3aed;
        opacity: 0.5;
    }

    #emptyQuestions h4 {
        color: #374151;
        font-weight: 600;
        margin-top: 1rem !important;
    }

    #emptyQuestions p {
        color: #9ca3af;
    }

    /* Add Question Area */
    .border.rounded.mt-4.p-4.text-center {
        border: 2px dashed #d1d5db !important;
        border-radius: 12px !important;
        padding: 2rem !important;
        text-align: center;
        transition: all 0.3s ease;
        background: #fafafa;
        cursor: pointer;
        margin-top: 1rem !important;
    }

    .border.rounded.mt-4.p-4.text-center:hover {
        border-color: #7c3aed !important;
        background: #f5f3ff;
    }

    .border.rounded.mt-4.p-4.text-center i {
        color: #7c3aed;
        opacity: 0.6;
    }

    .border.rounded.mt-4.p-4.text-center h5 {
        color: #374151;
        font-weight: 500;
        margin-top: 0.5rem !important;
    }

    .border.rounded.mt-4.p-4.text-center p {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Complexity Review Panel */
    .panel[style*="background:#f5f3ff"] {
        border-left: 4px solid #7c3aed !important;
        background: #f5f3ff !important;
    }

    .panel[style*="background:#f5f3ff"] h4 {
        color: #5b21b6 !important;
        font-weight: 700 !important;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .panel[style*="background:#f5f3ff"] h4::before {
        content: '💡';
        font-size: 1.25rem;
    }

    /* Action Bar */
    .d-flex.justify-content-between.mb-5 {
        padding: 1.5rem 0;
        border-top: 1px solid #e5e7eb;
        margin-top: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .dash-header {
            padding: 1.5rem;
        }

        .dash-header h1 {
            font-size: 1.5rem;
        }

        .panel .panel-body {
            padding: 1rem;
        }

        .d-flex.justify-content-between.mb-5 {
            flex-direction: column;
        }

        .d-flex.justify-content-between.mb-5 > div {
            width: 100%;
            display: flex;
            gap: 0.5rem;
        }

        .d-flex.justify-content-between.mb-5 > div .btn {
            flex: 1;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .question-card .card-header {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .panel .panel-head {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .panel .panel-head .panel-actions {
            width: 100%;
        }

        .panel .panel-head .panel-actions .btn {
            flex: 1;
            justify-content: center;
        }
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .panel {
        animation: slideInUp 0.4s ease-out;
    }

    .panel:nth-child(2) {
        animation-delay: 0.05s;
    }
    .panel:nth-child(3) {
        animation-delay: 0.1s;
    }
    .panel:nth-child(4) {
        animation-delay: 0.15s;
    }
    .panel:nth-child(5) {
        animation-delay: 0.2s;
    }

    /* Scrollable question container */
    #questionContainer {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    #questionContainer::-webkit-scrollbar {
        width: 6px;
    }

    #questionContainer::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }

    #questionContainer::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    #questionContainer::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role' => 'lecturer',
        'user' => auth()->user()
    ])

    <div class="dash-main">

        <div class="dash-header mb-4">
            <div>
                <div class="text-muted small">
                    <i class="ti ti-layout-dashboard"></i> Dashboard / Quizzes / Edit
                </div>

                <h1 class="fw-bold mt-2">
                    Edit & Schedule Quiz
                    <span style="font-size: 0.7rem; background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 500;">
                        ID: #{{ $quiz->quiz_id }}
                    </span>
                </h1>

                <p class="text-muted">
                    Configure your assessment settings, define your target audience,
                    and manage your examination.
                </p>
            </div>
        </div>

        <div class="dash-body">

            <form method="POST" action="{{ route('lecturer.quizzes.update', $quiz->quiz_id) }}">
                @csrf
                @method('PUT')

               
                <div class="panel mb-4">
                    <div class="panel-head">
                        <h3 class="panel-title">
                            <i class="ti ti-file-text"></i>
                            Quiz Essentials
                        </h3>
                        <span style="font-size: 0.65rem; color: #9ca3af; background: #f3f4f6; padding: 0.25rem 0.75rem; border-radius: 12px;">
                            Required Fields
                        </span>
                    </div>

                    <div class="panel-body">
                        <div class="form-group mb-3">
                            <label class="form-label">
                                Quiz Title
                                <span class="required">*</span>
                                <span class="helper">A clear, descriptive title</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                class="form-control"
                                value="{{ old('title', $quiz->title) }}"
                                placeholder="e.g., Java Programming Fundamentals"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">
                                Target Student Category
                                <span class="required">*</span>
                            </label>
                            <select
                                name="target_category"
                                class="form-control"
                                required>
                                <option value="Level 100"
                                    {{ $quiz->target_category == 'Level 100' ? 'selected' : '' }}>
                                    🎓 Undergraduate - Java Students
                                </option>
                                <option value="Level 200"
                                    {{ $quiz->target_category == 'Level 200' ? 'selected' : '' }}>
                                    🎓 Undergraduate - Data Structures Students
                                </option>
                                <option value="Level 300"
                                    {{ $quiz->target_category == 'Level 300' ? 'selected' : '' }}>
                                    🎓 Undergraduate - Algorithms Students
                                </option>
                                <option value="Masters"
                                    {{ $quiz->target_category == 'Masters' ? 'selected' : '' }}>
                                    🎓 Masters
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Quiz Duration
                                <span class="required">*</span>
                                <span class="helper">Time limit in minutes</span>
                            </label>
                            <div class="d-flex align-items-center">
                                <input
                                    type="number"
                                    name="duration_mins"
                                    class="form-control"
                                    value="{{ old('duration_mins', $quiz->duration_mins) }}"
                                    min="1"
                                    max="180"
                                    style="width: 120px;"
                                    required>
                                <span class="ms-2" style="color: #6b7280; font-weight: 500;">
                                    minutes
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

               
                <div class="panel mb-4">
                    <div class="panel-head">
                        <h3 class="panel-title">
                            <i class="ti ti-calendar-event"></i>
                            Schedule
                        </h3>
                        <span style="font-size: 0.65rem; color: #9ca3af; background: #f3f4f6; padding: 0.25rem 0.75rem; border-radius: 12px;">
                            Optional
                        </span>
                    </div>

                    <div class="panel-body">
                        <div class="form-group mb-3">
                            <label class="form-label">
                                Start Date & Time
                                <span class="helper">When the quiz becomes available</span>
                            </label>
                            <input
                                type="datetime-local"
                                name="start_time"
                                class="form-control"
                                value="{{ old('start_time', $quiz->start_time) }}">
                        </div>

                       
                    </div>
                </div>

                
                <div class="panel mb-4" style="background:#f5f3ff;border-left:5px solid #7c3aed;">
                    <div class="panel-body">
                        <h4 style="color:#5b21b6;font-weight:700;display:flex;align-items:center;gap:0.5rem;">
                            Complexity Review
                        </h4>
                        <p class="text-muted mb-0">
                            Students note that the current Quiz will contribute <strong>20%</strong> to your course work results.
                            Therefore attempt them with maximum desire.
                        </p>
                    </div>
                </div>

                
                <div class="panel mb-4">
                    <div class="panel-head d-flex justify-content-between align-items-center">
                        <h3 class="panel-title">
                            <i class="ti ti-list-check"></i>
                            Question Builder
                            <span style="font-size: 0.7rem; background: #ede9fe; color: #7c3aed; padding: 0.125rem 0.5rem; border-radius: 10px; margin-left: 0.5rem;">
                                {{ $quiz->questions->count() }} questions
                            </span>
                        </h3>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('lecturer.quizzes.upload.form', $quiz) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-file-upload"></i>
                                Import CSV
                            </a>
                            <button
                                type="button"
                                id="addQuestion"
                                class="btn btn-primary btn-sm">
                                <i class="ti ti-plus"></i>
                                New Question
                            </button>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div id="questionContainer">
                            @forelse($quiz->questions as $index => $question)
                            <div class="question-card card shadow-sm mb-4">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <strong class="question-number">
                                        <span>📝 MCQ #{{ $index + 1 }}</span>
                                        <span class="badge">Question</span>
                                    </strong>
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger remove-question">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label>Question Text</label>
                                        <textarea
                                            class="form-control"
                                            rows="2"
                                            name="questions[{{ $index }}][question]"
                                            placeholder="Enter your question here">{{ $question->question }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Option A</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="questions[{{ $index }}][option_a]"
                                                value="{{ $question->option_a }}"
                                                placeholder="Option A">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Option B</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="questions[{{ $index }}][option_b]"
                                                value="{{ $question->option_b }}"
                                                placeholder="Option B">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Option C</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="questions[{{ $index }}][option_c]"
                                                value="{{ $question->option_c }}"
                                                placeholder="Option C">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Option D</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="questions[{{ $index }}][option_d]"
                                                value="{{ $question->option_d }}"
                                                placeholder="Option D">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Correct Answer</label>
                                        <select
                                            class="form-control"
                                            name="questions[{{ $index }}][correct_answer]">
                                            <option value="">Select correct answer...</option>
                                            <option value="A" {{ $question->correct_answer == "A" ? "selected" : "" }}>A</option>
                                            <option value="B" {{ $question->correct_answer == "B" ? "selected" : "" }}>B</option>
                                            <option value="C" {{ $question->correct_answer == "C" ? "selected" : "" }}>C</option>
                                            <option value="D" {{ $question->correct_answer == "D" ? "selected" : "" }}>D</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div id="emptyQuestions" class="text-center p-5 border rounded bg-light">
                                <i class="ti ti-help-circle" style="font-size:60px;color:#7c3aed;opacity:0.5;"></i>
                                <h4 class="mt-3" style="color:#374151;font-weight:600;">
                                    No Questions Added Yet
                                </h4>
                                <p class="text-muted">
                                    Click <strong>New Question</strong> to add your first question or import from CSV.
                                </p>
                            </div>
                            @endforelse
                        </div>

                        <div class="border rounded mt-4 p-4 text-center" style="border-style:dashed !important;cursor:pointer;" onclick="document.getElementById('addQuestion').click()">
                            <i class="ti ti-plus" style="font-size:40px;color:#7c3aed;opacity:0.6;"></i>
                            <h5 class="mt-2" style="color:#374151;font-weight:500;">
                                Add Question to this Quiz
                            </h5>
                            <p class="text-muted">
                                Press the <strong>New Question</strong> button above or click here
                            </p>
                        </div>
                    </div>
                </div>

                
                <div class="d-flex justify-content-between mb-5">
                    <button
                        type="submit"
                        name="draft"
                        class="btn btn-outline-secondary">
                        <i class="ti ti-device-floppy"></i>
                        Save as Draft
                    </button>

                    <div>
                        <a
                            href="{{ route('lecturer.quizzes') }}"
                            class="btn btn-light">
                            <i class="ti ti-x"></i>
                            Cancel
                        </a>
                        <button
                            class="btn btn-primary"
                            type="submit">
                            <i class="ti ti-calendar-event"></i>
                            Schedule Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let questionIndex = {{ $quiz->questions->count() }};
    const container = document.getElementById('questionContainer');
    const addButton = document.getElementById('addQuestion');

    // Update question numbers
    function updateNumbers() {
        document.querySelectorAll('.question-card').forEach((card, index) => {
            const numberSpan = card.querySelector('.question-number');
            if (numberSpan) {
                numberSpan.innerHTML = `
                    <span>📝 MCQ #${index + 1}</span>
                    <span class="badge">Question</span>
                `;
            }
        });
    }

    // Add new question
    addButton.addEventListener('click', function() {
        const empty = document.getElementById('emptyQuestions');
        if (empty) {
            empty.remove();
        }

        const html = `
            <div class="question-card card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong class="question-number">
                        <span>📝 MCQ #${questionIndex + 1}</span>
                        <span class="badge">Question</span>
                    </strong>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger remove-question">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Question Text</label>
                        <textarea
                            class="form-control"
                            rows="2"
                            name="questions[${questionIndex}][question]"
                            placeholder="Enter your question here"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Option A</label>
                            <input
                                type="text"
                                class="form-control"
                                name="questions[${questionIndex}][option_a]"
                                placeholder="Option A">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Option B</label>
                            <input
                                type="text"
                                class="form-control"
                                name="questions[${questionIndex}][option_b]"
                                placeholder="Option B">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Option C</label>
                            <input
                                type="text"
                                class="form-control"
                                name="questions[${questionIndex}][option_c]"
                                placeholder="Option C">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Option D</label>
                            <input
                                type="text"
                                class="form-control"
                                name="questions[${questionIndex}][option_d]"
                                placeholder="Option D">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Correct Answer</label>
                        <select class="form-control" name="questions[${questionIndex}][correct_answer]">
                            <option value="">Select correct answer...</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        questionIndex++;
        updateNumbers();

        // Scroll to new question
        const newQuestion = container.lastElementChild;
        if (newQuestion) {
            newQuestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Remove question
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-question');
        if (!btn) return;

        const card = btn.closest('.question-card');
        if (card) {
            if (confirm('Are you sure you want to remove this question?')) {
                card.remove();
                updateNumbers();

                // Show empty state if no questions
                if (document.querySelectorAll('.question-card').length === 0) {
                    const emptyHtml = `
                        <div id="emptyQuestions" class="text-center p-5 border rounded bg-light">
                            <i class="ti ti-help-circle" style="font-size:60px;color:#7c3aed;opacity:0.5;"></i>
                            <h4 class="mt-3" style="color:#374151;font-weight:600;">
                                No Questions Added Yet
                            </h4>
                            <p class="text-muted">
                                Click <strong>New Question</strong> to add your first question or import from CSV.
                            </p>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', emptyHtml);
                }
            }
        }
    });
});
</script>

@endsection