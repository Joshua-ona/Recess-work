@extends('layouts.app')
@section('title', 'System Analytics')
@section('body')

<div class="dash-wrap">
    @include('layouts.sidebar', [
    'role' => 'system_admin',
    'user' => auth()->user(),
    ])
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title">System analytics</div>
                <div class="dash-header-sub">Usage over the last {{ $days }} days</div>
            </div>
        </div>

        <div class="dash-body" style="flex-direction:column;">

            {{-- Secondary tabs for the two analytics views --}}
            <div style="display:flex; gap:8px; margin-bottom:1.5rem;">
                <a href="{{ route('admin.analytics') }}"
                    class="btn btn-sm {{ request()->routeIs('admin.analytics') ? 'btn-primary' : 'btn-outline' }}">System
                    analytics</a>
                <a href="{{ route('admin.analytics.overview') }}"
                    class="btn btn-sm {{ request()->routeIs('admin.analytics.overview') ? 'btn-primary' : 'btn-outline' }}">Student
                    analytics</a>
            </div>

            {{-- Stat cards --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--purple-50);">
                            <i class="ti ti-activity" style="color:var(--purple-600)" aria-hidden="true"></i>
                        </span>
                        Active users today
                    </div>
                    <div class="stat-value">{{ $active_today }}</div>
                    @if($active_change_pct !== null)
                    <div class="stat-change {{ $active_change_pct >= 0 ? 'text-pos' : 'text-neg' }}">
                        {{ $active_change_pct >= 0 ? '▲' : '▼' }} {{ abs($active_change_pct) }}% vs yesterday
                    </div>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--blue-50);">
                            <i class="ti ti-message-circle" style="color:var(--blue-400)" aria-hidden="true"></i>
                        </span>
                        Content actions today
                    </div>
                    <div class="stat-value">{{ $content_today }}</div>
                    <p style="font-size:11px; color:var(--muted); margin:2px 0 0;">posts, replies, quizzes, messages</p>
                    @if($content_change_pct !== null)
                    <div class="stat-change {{ $content_change_pct >= 0 ? 'text-pos' : 'text-neg' }}">
                        {{ $content_change_pct >= 0 ? '▲' : '▼' }} {{ abs($content_change_pct) }}% vs yesterday
                    </div>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--amber-50);">
                            <i class="ti ti-alert-triangle" style="color:var(--amber-400)" aria-hidden="true"></i>
                        </span>
                        Warned users
                    </div>
                    <div class="stat-value" style="color:var(--amber-400);">{{ $total_warned }}</div>
                    <p style="font-size:11px; color:var(--muted); margin:2px 0 0;">{{ $total_warnings }} warnings issued
                        total</p>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--red-50);">
                            <i class="ti ti-ban" style="color:var(--red-400)" aria-hidden="true"></i>
                        </span>
                        Blacklisted
                    </div>
                    <div class="stat-value" style="color:var(--red-600);">{{ $total_blacklisted }}</div>
                </div>
            </div>

            <div class="panel-grid" style="margin-bottom:1rem;">
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">Daily active users</span></div>
                    <div class="panel-body"><canvas id="activeChart" height="200"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">Content activity</span></div>
                    <div class="panel-body"><canvas id="contentChart" height="200"></canvas></div>
                </div>
            </div>

            <div class="panel-grid" style="margin-bottom:1rem;">
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">New signups</span></div>
                    <div class="panel-body"><canvas id="signupChart" height="180"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">Warnings issued</span></div>
                    <div class="panel-body"><canvas id="warningChart" height="180"></canvas></div>
                </div>
            </div>

            <div class="panel-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">Users by role</span></div>
                    <div class="panel-body"><canvas id="roleChart" height="200"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">Moderation status</span></div>
                    <div class="panel-body"><canvas id="statusChart" height="200"></canvas></div>
                </div>
                <div class="panel">
                    <div class="panel-head"><span class="panel-title">Most flagged users</span></div>
                    <div class="panel-body">
                        @if($top_flagged->isEmpty())
                        <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No users have
                            been warned.</p>
                        @else
                        @foreach($top_flagged as $u)
                        <div
                            style="display:flex; align-items:center; justify-content:space-between; font-size:13px; padding:6px 0;">
                            <span>{{ $u->first_name }} {{ $u->last_name }}</span>
                            <span class="badge {{ $u->status === 'blacklisted' ? 'badge-red' : 'badge-amber' }}">
                                {{ $u->warning_count }} {{ Str::plural('warning', $u->warning_count) }}
                            </span>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lineOpts = {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    };

    new Chart(document.getElementById('activeChart'), {
        type: 'line',
        data: {
            labels: {
                !!json_encode($active_trend['labels']) !!
            },
            datasets: [{
                label: 'Active users',
                data: {
                    !!json_encode($active_trend['counts']) !!
                },
                borderColor: '#534AB7',
                backgroundColor: 'rgba(83,74,183,0.12)',
                tension: 0.3,
                fill: true,
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('contentChart'), {
        type: 'bar',
        data: {
            labels: {
                !!json_encode($content_trend['labels']) !!
            },
            datasets: [{
                label: 'Actions',
                data: {
                    !!json_encode($content_trend['counts']) !!
                },
                backgroundColor: '#378ADD',
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('signupChart'), {
        type: 'bar',
        data: {
            labels: {
                !!json_encode($signup_trend['labels']) !!
            },
            datasets: [{
                label: 'Signups',
                data: {
                    !!json_encode($signup_trend['counts']) !!
                },
                backgroundColor: '#1D9E75',
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('warningChart'), {
        type: 'bar',
        data: {
            labels: {
                !!json_encode($warning_trend['labels']) !!
            },
            datasets: [{
                label: 'Warnings',
                data: {
                    !!json_encode($warning_trend['counts']) !!
                },
                backgroundColor: '#E24B4A',
            }],
        },
        options: lineOpts,
    });

    new Chart(document.getElementById('roleChart'), {
        type: 'doughnut',
        data: {
            labels: {
                !!json_encode($role_distribution['labels']) !!
            },
            datasets: [{
                data: {
                    !!json_encode($role_distribution['counts']) !!
                },
                backgroundColor: ['#534AB7', '#378ADD', '#BA7517'],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        },
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: {
                !!json_encode($status_distribution['labels']) !!
            },
            datasets: [{
                data: {
                    !!json_encode($status_distribution['counts']) !!
                },
                backgroundColor: ['#1D9E75', '#BA7517', '#E24B4A', '#9b9a96'],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        },
    });
});
</script>

@endsection