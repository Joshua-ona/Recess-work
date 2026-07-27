@extends('layouts.app')

@section('title', 'Quiz Management')

@push('styles')
<style>
    /* Modern Quiz Management Styles */
    .quiz-management-container {
        padding: 2rem 1.5rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #eef2f7 100%);
        min-height: 100vh;
    }

    /* Header Section */
    .quiz-header {
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

    .quiz-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .quiz-header::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -5%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }

    .quiz-header-content {
        position: relative;
        z-index: 1;
    }

    .quiz-header-title {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        letter-spacing: -0.5px;
    }

    .quiz-header-title i {
        font-size: 1.75rem;
    }

    .quiz-header-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.95rem;
        margin: 0.25rem 0 0 0;
        font-weight: 400;
    }

    .btn-create-quiz {
        background: white;
        color: #764ba2;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-create-quiz:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        color: #5b21b6;
    }

    .btn-create-quiz:active {
        transform: translateY(0px) scale(0.98);
    }

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-item {
        background: white;
        padding: 1.25rem 1.5rem;
        border-radius: 12px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.12);
        border-color: #667eea;
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .stat-icon {
        display: inline-block;
        margin-right: 0.5rem;
        font-size: 1.25rem;
    }

    /* Success Alert */
    .alert-custom {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideDown 0.5s ease-out;
    }

    .alert-custom i {
        font-size: 1.25rem;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Card */
    .card-custom {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-custom:hover {
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }

    .card-custom .card-body {
        padding: 0;
    }

    /* Table */
    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .table-custom thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .table-custom thead th {
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .table-custom tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-custom tbody tr:last-child {
        border-bottom: none;
    }

    .table-custom tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: scale(1.002);
    }

    .table-custom tbody td {
        padding: 1rem 1.5rem;
        font-size: 0.9rem;
        color: #1e293b;
        vertical-align: middle;
    }

    /* Quiz Title in Table */
    .quiz-title-custom {
        font-weight: 600;
        color: #0f172a;
    }

    .quiz-meta {
        display: block;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.125rem;
    }

    .quiz-meta i {
        font-size: 0.7rem;
    }

    /* Category Badge */
    .category-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        background: #eef2ff;
        color: #4338ca;
    }

    .category-badge.level100 { background: #dbeafe; color: #1e40af; }
    .category-badge.level200 { background: #d1fae5; color: #065f46; }
    .category-badge.level300 { background: #fef3c7; color: #92400e; }
    .category-badge.masters { background: #f3e8ff; color: #6b21a8; }

    /* Status Badges */
    .status-badge-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.875rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .status-badge-custom.published {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge-custom.published::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        animation: pulse 2s infinite;
    }

    .status-badge-custom.draft {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge-custom.draft::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #f59e0b;
        display: inline-block;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }

    /* Action Buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        background: #eef2ff;
        color: #4338ca;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 56, 202, 0.2);
        background: #e0e7ff;
    }

    .action-btn i {
        font-size: 0.9rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(135deg, #fafbfc 0%, #f1f5f9 100%);
        border-radius: 12px;
        margin: 1rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #94a3b8;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    .empty-state .btn-create-first {
        display: inline-block;
        padding: 0.625rem 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }

    .empty-state .btn-create-first:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .quiz-management-container {
            padding: 1rem;
        }

        .quiz-header {
            padding: 1.5rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .quiz-header-title {
            font-size: 1.5rem;
        }

        .btn-create-quiz {
            width: 100%;
            justify-content: center;
        }

        .stats-row {
            grid-template-columns: 1fr 1fr;
        }

        .table-custom thead th,
        .table-custom tbody td {
            padding: 0.75rem;
            font-size: 0.8rem;
        }

        .action-btn {
            padding: 0.25rem 0.625rem;
            font-size: 0.7rem;
        }

        .status-badge-custom {
            padding: 0.125rem 0.625rem;
            font-size: 0.65rem;
        }
    }

    @media (max-width: 480px) {
        .stats-row {
            grid-template-columns: 1fr;
        }

        .quiz-header-title {
            font-size: 1.25rem;
        }

        .table-custom {
            font-size: 0.75rem;
        }

        .table-custom thead th,
        .table-custom tbody td {
            padding: 0.5rem;
        }
    }

    /* Scrollable table on small screens */
    .table-responsive-custom {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive-custom::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive-custom::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .table-responsive-custom::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .table-responsive-custom::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('body')
<div class="quiz-management-container">

    <!-- Header -->
    <div class="quiz-header">
        <div class="quiz-header-content">
            <h1 class="quiz-header-title">
                <i class="ti ti-list-check"></i>
                Quiz Management
            </h1>
            <p class="quiz-header-subtitle">
                Manage your quizzes, track performance, and create new assessments.
            </p>
        </div>
        <a href="{{ route('lecturer.quizzes.create') }}" class="btn-create-quiz">
            <i class="ti ti-plus"></i>
            Create Quiz
        </a>
    </div>

    <!-- Stats -->
    @php
        $total = $quizzes->count();
        $published = $quizzes->where('is_published', true)->count();
        $drafts = $quizzes->where('is_published', false)->count();
        $active = $quizzes->filter(function($q) {
            return $q->is_published && ($q->start_time === null || $q->start_time <= now());
        })->count();
    @endphp

    <div class="stats-row">
        <div class="stat-item">
            <div>
                <span class="stat-icon">📚</span>
                <span class="stat-number">{{ $total }}</span>
            </div>
            <div class="stat-label">Total Quizzes</div>
        </div>
        <div class="stat-item">
            <div>
                <span class="stat-icon">✅</span>
                <span class="stat-number">{{ $published }}</span>
            </div>
            <div class="stat-label">Published</div>
        </div>
        <div class="stat-item">
            <div>
                <span class="stat-icon">📝</span>
                <span class="stat-number">{{ $drafts }}</span>
            </div>
            <div class="stat-label">Drafts</div>
        </div>
        <div class="stat-item">
            <div>
                <span class="stat-icon">⏳</span>
                <span class="stat-number">{{ $active }}</span>
            </div>
            <div class="stat-label">Active</div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-custom">
            <i class="ti ti-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="card-custom">
        <div class="card-body">
            @if($quizzes->count())
                <div class="table-responsive-custom">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <div class="quiz-title-custom">{{ $quiz->title }}</div>
                                        <span class="quiz-meta">
                                            <i class="ti ti-hash"></i> ID: {{ $quiz->quiz_id }}
                                            <span style="margin: 0 0.25rem;">•</span>
                                            {{ $quiz->questions->count() }} questions
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $categoryClass = '';
                                            if(str_contains($quiz->target_category, 'Level 100')) $categoryClass = 'level100';
                                            elseif(str_contains($quiz->target_category, 'Level 200')) $categoryClass = 'level200';
                                            elseif(str_contains($quiz->target_category, 'Level 300')) $categoryClass = 'level300';
                                            elseif(str_contains($quiz->target_category, 'Masters')) $categoryClass = 'masters';
                                        @endphp
                                        <span class="category-badge {{ $categoryClass }}">
                                            {{ $quiz->target_category }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($quiz->start_time)
                                            {{ \Carbon\Carbon::parse($quiz->start_time)->format('M d, Y H:i') }}
                                        @else
                                            <span style="color: #94a3b8; font-size: 0.8rem;">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-weight: 500;">
                                            {{ $quiz->duration_mins }}
                                            <span style="font-weight: 400; color: #94a3b8; font-size: 0.75rem;">min</span>
                                        </span>
                                    </td>
                                    <td>
                                        @if($quiz->is_published)
                                            <span class="status-badge-custom published">Published</span>
                                        @else
                                            <span class="status-badge-custom draft">Draft</span>
                                        @endif
                                    </td>
                                  <td>
                                    <a href="{{ route('lecturer.quizzes.edit', $quiz->quiz_id) }}" class="action-btn">
                                        <i class="ti ti-edit"></i>
                                                Edit
                                    </a>
                                        @if(!$quiz->is_published)
                                            <form action="{{ route('lecturer.quizzes.publish', $quiz->quiz_id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                    <button type="submit" class="action-btn" style="background:#d1fae5;color:#065f46;">
                                                        <i class="ti ti-send"></i>
                                                            Publish
                                                    </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="ti ti-file-empty"></i>
                    <h4>No Quizzes Created Yet</h4>
                    <p>Get started by creating your first quiz.</p>
                    <a href="{{ route('lecturer.quizzes.create') }}" class="btn-create-first">
                        <i class="ti ti-plus"></i> Create Your First Quiz
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection