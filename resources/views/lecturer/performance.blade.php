@extends('layouts.app')
@section('title', 'Performance Reports')

@section('body')

<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role' => 'lecturer',
        'user' => auth()->user(),
    ])

    <div class="dash-main">

        <div class="dash-header">
            <div>
                <div class="dash-header-title">Performance reports</div>
                <div class="dash-header-sub">
                    How your students are doing across quizzes and discussion topics.
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--purple-50);"><i class="ti ti-users" style="color:var(--purple-600);"></i></span>
                    My students
                </div>
                <div class="stat-value">{{ $student_count }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--teal-50);"><i class="ti ti-activity" style="color:var(--teal-600);"></i></span>
                    Engagement rate
                </div>
                <div class="stat-value">{{ $engagement_rate_pct }}%</div>
                @if($engagement_rate_change_pct !== null)
                    <div class="stat-change {{ $engagement_rate_change_pct >= 0 ? 'text-pos' : 'text-neg' }}">
                        <i class="ti ti-trending-{{ $engagement_rate_change_pct >= 0 ? 'up' : 'down' }}"></i>
                        {{ $engagement_rate_change_pct >= 0 ? '+' : '' }}{{ $engagement_rate_change_pct }}% vs previous period
                    </div>
                @endif
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--blue-50);"><i class="ti ti-clipboard-check" style="color:var(--blue-600);"></i></span>
                    Quizzes created
                </div>
                <div class="stat-value">{{ $quizzes->count() }}</div>
                @if($quiz_trend_delta !== null)
                    <div class="stat-change {{ $quiz_trend_delta >= 0 ? 'text-pos' : 'text-neg' }}">
                        <i class="ti ti-trending-{{ $quiz_trend_delta >= 0 ? 'up' : 'down' }}"></i>
                        latest avg {{ $quiz_trend_delta >= 0 ? '+' : '' }}{{ $quiz_trend_delta }}% vs prior quizzes
                    </div>
                @endif
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--amber-50);"><i class="ti ti-messages" style="color:var(--amber-600);"></i></span>
                    Avg replies / topic
                </div>
                <div class="stat-value">{{ $avg_replies_per_topic }}</div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="panel">
                <div class="panel-head"><span class="panel-title">Average quiz score over time</span></div>
                <div class="panel-body">
                    @if($quizzes->isEmpty())
                        <p class="text-muted" style="font-size:13px;">You haven't published any quizzes yet.</p>
                    @else
                        <canvas id="quizTrendChart" height="220"></canvas>
                    @endif
                </div>
            </div>
            <div class="panel">
                <div class="panel-head"><span class="panel-title">Score distribution (all attempts)</span></div>
                <div class="panel-body">
                    <canvas id="scoreDistChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <div class="full-panel" style="margin-bottom:1rem;">
            <div class="panel-head"><span class="panel-title">Student engagement, last {{ count($engagement_trend['labels']) }} days</span></div>
            <div class="panel-body">
                <canvas id="engagementChart" height="140"></canvas>
            </div>
        </div>

        <div class="panel-grid">

            {{-- Students table --}}
            <div class="panel" style="grid-column: span 2;">
                <div class="panel-head"><span class="panel-title">Your students</span></div>
                <div class="panel-body table-scroll" style="padding:0;">
                    @if(count($students) === 0)
                        <p class="text-muted" style="font-size:13px;padding:1rem;">
                            No students have taken your quizzes, joined your groups, or replied to your discussions yet.
                        </p>
                    @else
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Avg score</th>
                                    <th>Quizzes attempted</th>
                                    <th>Replies</th>
                                    <th>Last active</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $s)
                                <tr>
                                    <td>{{ $s['name'] }}</td>
                                    <td>
                                        @if($s['avg_pct'] !== null)
                                            <span class="badge {{ $s['avg_pct'] >= 70 ? 'badge-green' : ($s['avg_pct'] >= 40 ? 'badge-amber' : 'badge-red') }}">{{ $s['avg_pct'] }}%</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $s['quizzes_attempted'] }}</td>
                                    <td>{{ $s['replies'] }}</td>
                                    <td class="text-muted">{{ $s['last_active'] ? \Carbon\Carbon::parse($s['last_active'])->diffForHumans() : 'Never' }}</td>
                                    <td><a href="{{ route('lecturer.students.show', $s['id']) }}" class="link-sm">View</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Discussion topics --}}
        <div class="full-panel">
            <div class="panel-head"><span class="panel-title">Your discussion topics</span></div>
            <div class="panel-body table-scroll" style="padding:0;">
                @if($my_discussions->isEmpty())
                    <p class="text-muted" style="font-size:13px;padding:1rem;">You haven't started any discussion topics yet.</p>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Replies</th>
                                <th>Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($my_discussions as $d)
                            <tr>
                                <td>{{ $d->title }}</td>
                                <td><span class="badge badge-blue">{{ $d->replies_count }}</span></td>
                                <td class="text-muted">{{ $d->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($quizzes->isNotEmpty())
    new Chart(document.getElementById('quizTrendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($quizzes->pluck('title')) !!},
            datasets: [{
                label: 'Avg score (%)',
                data: {!! json_encode($quizzes->pluck('avg_pct')) !!},
                borderColor: '#534AB7',
                backgroundColor: 'rgba(83,74,183,0.12)',
                tension: 0.3,
                fill: true,
                spanGaps: true,
            }],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } },
        },
    });
    @endif

    new Chart(document.getElementById('scoreDistChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($score_distribution['labels']) !!},
            datasets: [{
                label: 'Attempts',
                data: {!! json_encode($score_distribution['counts']) !!},
                backgroundColor: ['#E24B4A', '#BA7517', '#378ADD', '#1D9E75'],
            }],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } },
        },
    });

    new Chart(document.getElementById('engagementChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($engagement_trend['labels']) !!},
            datasets: [{
                label: 'Students active',
                data: {!! json_encode($engagement_trend['counts']) !!},
                borderColor: '#0F6E56',
                backgroundColor: 'rgba(15,110,86,0.12)',
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } },
        },
    });
});
</script>
@endpush
