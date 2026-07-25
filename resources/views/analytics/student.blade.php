@extends('layouts.app')
@section('title', isset($backRoute) && $backRoute ? ($student->full_name . ' — Analytics') : 'My Analytics')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role' => $role === 'system_admin' ? 'admin' : $role,
        'user' => auth()->user(),
    ])

    <div class="dash-main">

        <div class="dash-header">
            <div>
                @if(!empty($backRoute))
                    <a href="{{ $backRoute }}" class="link-sm" style="display:inline-flex;align-items:center;gap:4px;margin-bottom:6px;">
                        <i class="ti ti-arrow-left"></i> {{ $backLabel ?? 'Back' }}
                    </a>
                @endif
                <div class="dash-header-title">
                    @if(!empty($backRoute))
                        {{ $student->full_name }}'s analytics
                    @else
                        My analytics
                    @endif
                </div>
                <div class="dash-header-sub">
                    Quiz performance, discussion activity, and group participation.
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--purple-50);"><i class="ti ti-star" style="color:var(--purple-600);"></i></span>
                    Overall score
                </div>
                <div class="stat-value">{{ $overall_score }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--teal-50);"><i class="ti ti-clipboard-check" style="color:var(--teal-600);"></i></span>
                    Avg quiz score
                </div>
                <div class="stat-value">{{ $overall_avg_pct }}%</div>
                @if($trend_delta !== null)
                    <div class="stat-change {{ $trend_delta >= 0 ? 'text-pos' : 'text-neg' }}">
                        <i class="ti ti-trending-{{ $trend_delta >= 0 ? 'up' : 'down' }}"></i> {{ $trend_delta >= 0 ? '+' : '' }}{{ $trend_delta }}% vs earlier attempts
                    </div>
                @endif
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--blue-50);"><i class="ti ti-list-check" style="color:var(--blue-600);"></i></span>
                    Quizzes taken
                </div>
                <div class="stat-value">{{ $quizzes_taken }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--amber-50);"><i class="ti ti-messages" style="color:var(--amber-600);"></i></span>
                    Discussions started
                </div>
                <div class="stat-value">{{ $discussions_started }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--pink-50);"><i class="ti ti-message-2" style="color:var(--pink-600);"></i></span>
                    Replies posted
                </div>
                <div class="stat-value">{{ $replies_posted }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--green-50);"><i class="ti ti-users-group" style="color:var(--green-600);"></i></span>
                    Group participation
                </div>
                <div class="stat-value">{{ $participation_rate !== null ? $participation_rate.'%' : '—' }}</div>
                <div class="stat-change text-muted">of discussions in {{ $groups_joined }} joined {{ Str::plural('group', $groups_joined) }}</div>
            </div>
        </div>

        <div class="panel-grid">

            {{-- Quiz score trend --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Quiz performance vs group average</span>
                </div>
                <div class="panel-body">
                    @if($quiz_labels->isEmpty())
                        <p class="text-muted" style="font-size:13px;">No quizzes attempted yet.</p>
                    @else
                        <canvas id="quizScoreChart" height="220"></canvas>
                    @endif
                </div>
            </div>

            {{-- Discussion / reply activity --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Discussion activity (last {{ count($discussion_trend['labels']) }} days)</span>
                </div>
                <div class="panel-body">
                    <canvas id="activityChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="panel">
                <div class="panel-head"><span class="panel-title">Strongest quiz</span></div>
                <div class="panel-body">
                    @if($best_quiz)
                        <p style="font-size:14px;font-weight:600;">{{ $best_quiz }}</p>
                        <div class="progress-wrap" style="margin-top:8px;margin-bottom:0;">
                            <div class="progress-label"><span>Score</span><span>{{ $best_quiz_pct }}%</span></div>
                            <div class="progress-bar"><div class="progress-fill" style="width:{{ $best_quiz_pct }}%;background:var(--teal-600);"></div></div>
                        </div>
                    @else
                        <p class="text-muted" style="font-size:13px;">No data yet.</p>
                    @endif
                </div>
            </div>
            <div class="panel">
                <div class="panel-head"><span class="panel-title">Needs the most work</span></div>
                <div class="panel-body">
                    @if($weakest_quiz)
                        <p style="font-size:14px;font-weight:600;">{{ $weakest_quiz }}</p>
                        <div class="progress-wrap" style="margin-top:8px;margin-bottom:0;">
                            <div class="progress-label"><span>Score</span><span>{{ $weakest_quiz_pct }}%</span></div>
                            <div class="progress-bar"><div class="progress-fill" style="width:{{ $weakest_quiz_pct }}%;background:var(--red-400);"></div></div>
                        </div>
                    @else
                        <p class="text-muted" style="font-size:13px;">No data yet.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($quiz_labels->isNotEmpty())
    new Chart(document.getElementById('quizScoreChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($quiz_labels) !!},
            datasets: [
                {
                    label: 'My score (%)',
                    data: {!! json_encode($my_scores) !!},
                    borderColor: '#534AB7',
                    backgroundColor: 'rgba(83,74,183,0.12)',
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: 'Group average (%)',
                    data: {!! json_encode($class_averages) !!},
                    borderColor: '#9b9a96',
                    borderDash: [5, 4],
                    tension: 0.3,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { position: 'bottom' } },
        },
    });
    @endif

    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($discussion_trend['labels']) !!},
            datasets: [
                {
                    label: 'Discussions started',
                    data: {!! json_encode($discussion_trend['counts']) !!},
                    backgroundColor: '#BA7517',
                },
                {
                    label: 'Replies posted',
                    data: {!! json_encode($reply_trend['counts']) !!},
                    backgroundColor: '#1D9E75',
                },
            ],
        },
        options: {
            responsive: true,
            scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { position: 'bottom' } },
        },
    });
});
</script>
@endpush
