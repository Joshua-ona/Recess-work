@extends('layouts.app')

@section('title', 'Create Quiz')

@push('styles')
<style>
    /* Modern Create Quiz Styles */
    .create-quiz-container {
        padding: 2rem 1.5rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #eef2f7 100%);
        min-height: 100vh;
    }

    /* Header Section */
    .quiz-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .quiz-header-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .quiz-header-modern::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -5%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }

    .header-left {
        position: relative;
        z-index: 1;
    }

    .header-left h2 {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        letter-spacing: -0.5px;
    }

    .header-left h2 i {
        font-size: 1.75rem;
    }

    .header-left p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.95rem;
        margin: 0.25rem 0 0 0;
        font-weight: 400;
    }

    .btn-return {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        padding: 0.625rem 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(10px);
    }

    .btn-return:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.4);
        color: white;
    }

    /* Panel */
    .panel-modern {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 2rem;
    }

    .panel-modern:hover {
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }

    .panel-modern .panel-head {
        padding: 1.25rem 2rem;
        border-bottom: 2px solid #f1f5f9;
        background: linear-gradient(135deg, #fafbfc 0%, #f8fafc 100%);
    }

    .panel-modern .panel-head .panel-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        margin: 0;
    }

    .panel-modern .panel-head .panel-title i {
        color: #667eea;
        font-size: 1.25rem;
    }

    .panel-modern .panel-body {
        padding: 2rem;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
        color: #1e293b;
        display: block;
        margin-bottom: 0.5rem;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        color: #0f172a;
        transition: all 0.3s ease;
        background: white;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-control:hover {
        border-color: #94a3b8;
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    /* Form Row */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 0;
    }

    .form-row .form-group {
        margin-bottom: 0;
    }

    /* Button */
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 2.5rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        margin-top: 0.5rem;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-save:active {
        transform: translateY(0px) scale(0.98);
    }

    .btn-save i {
        font-size: 1.1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .create-quiz-container {
            padding: 1rem;
        }

        .quiz-header-modern {
            padding: 1.5rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .header-left h2 {
            font-size: 1.5rem;
        }

        .btn-return {
            width: 100%;
            justify-content: center;
        }

        .panel-modern .panel-body {
            padding: 1.25rem;
        }

        .panel-modern .panel-head {
            padding: 1rem 1.25rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        .btn-save {
            width: 100%;
            justify-content: center;
            padding: 0.75rem 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .header-left h2 {
            font-size: 1.25rem;
        }

        .panel-modern .panel-body {
            padding: 1rem;
        }

        .form-control {
            font-size: 0.875rem;
            padding: 0.625rem 0.875rem;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .panel-modern {
        animation: fadeInUp 0.5s ease-out;
    }

    /* Input Icons */
    .form-group {
        position: relative;
    }

    /* Date input styling */
    input[type="datetime-local"] {
        appearance: none;
        padding: 0.75rem 1rem;
    }

    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        opacity: 0.5;
        cursor: pointer;
        padding: 0.25rem;
    }

    input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }

    /* Number input styling */
    input[type="number"] {
        appearance: textfield;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Required field indicator animation */
    .form-label .required {
        animation: pulse 2s infinite;
        display: inline-block;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
@endpush

@section('body')

<div class="create-quiz-container">

    <!-- Header -->
    <div class="quiz-header-modern">
        <div class="header-left">
            <h2>
                <i class="ti ti-plus"></i>
                Create Quiz
            </h2>
            <p>Fill in the quiz details below.</p>
        </div>
        <a href="{{ route('lecturer.quizzes') }}" class="btn-return">
            <i class="ti ti-arrow-left"></i>
            Return
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('lecturer.quizzes.store') }}" method="POST">
        @csrf

        <div class="panel-modern">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-file-text"></i>
                    Quiz Information
                </div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label class="form-label">
                        Quiz Title
                        <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        placeholder="Enter quiz title"
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Quiz ID
                            <span class="required">*</span>
                        </label>
                        <input
                            type="number"
                            name="group_id"
                            class="form-control"
                            placeholder="Enter quiz ID"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Category
                            <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="target_category"
                            class="form-control"
                            placeholder="Enter category"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Start Time
                            <span class="required">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="start_time"
                            class="form-control"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Duration (minutes)
                            <span class="required">*</span>
                        </label>
                        <input
                            type="number"
                            name="duration_mins"
                            class="form-control"
                            placeholder="Enter duration in minutes"
                            min="1"
                            max="180"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="ti ti-device-floppy"></i>
                    Save Quiz
                </button>
            </div>
        </div>

    </form>

</div>

@endsection