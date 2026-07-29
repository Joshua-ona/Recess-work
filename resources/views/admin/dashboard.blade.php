@extends('layouts.app')
@section('title', 'Admin dashboard')
@section('body')

<div class="dash-wrap">
    @include('layouts.sidebar', [
    'role' => 'system_admin',
    'user' => auth()->user(),
    'userCount' => $totalMembers ?? 0,
    ])
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title">Admin dashboard</div>
                <div class="dash-header-sub">{{ auth()->user()->full_name }}</div>
            </div>
        </div>

        <div class="dash-body" style="flex-direction:column;">

            @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            {{-- Stat cards --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--purple-50);">
                            <i class="ti ti-users" style="color:var(--purple-600)" aria-hidden="true"></i>
                        </span>
                        Total members
                    </div>
                    <div class="stat-value">{{ $totalMembers }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--teal-50);">
                            <i class="ti ti-activity" style="color:var(--teal-400)" aria-hidden="true"></i>
                        </span>
                        Active today
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
                        <span class="stat-icon" style="background:var(--amber-50);">
                            <i class="ti ti-hourglass" style="color:var(--amber-400)" aria-hidden="true"></i>
                        </span>
                        Pending approvals
                    </div>
                    <div class="stat-value" style="color:var(--amber-600);">{{ $pendingApprovals->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <span class="stat-icon" style="background:var(--red-50);">
                            <i class="ti ti-ban" style="color:var(--red-400)" aria-hidden="true"></i>
                        </span>
                        Blacklisted
                    </div>
                    <div class="stat-value" style="color:var(--red-600);">{{ $blacklistedCount }}</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 1rem; align-items:start;">

                {{-- Left column --}}
                <div style="display:flex; flex-direction:column; gap:1rem;">

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">Top contributors this month</span></div>
                        <div class="panel-body">
                            @foreach ($topContributors as $c)
                            <div class="progress-wrap">
                                <div class="progress-label">
                                    <span>{{ $c['name'] }}</span>
                                    <span>{{ $c['posts'] }} posts</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width: {{ min(100, $c['posts'] * 2.5) }}%; background: var(--green-600);">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">Flagged content</span></div>
                        <div class="panel-body">
                            @forelse ($flaggedContent ?? [] as $f)
                            <div class="disc-item" style="align-items:center; justify-content:space-between;">
                                <div>
                                    <div class="disc-title" style="white-space:normal;">{{ $f['title'] }}</div>
                                    <div class="disc-meta">{{ $f['meta'] }}</div>
                                </div>
                                <button class="btn btn-outline btn-sm">Review</button>
                            </div>
                            @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">Nothing
                                flagged right now.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">Upcoming quiz configurations</span></div>
                        <div class="panel-body">
                            <div class="table-scroll">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Quiz</th>
                                            <th>Category</th>
                                            <th style="text-align:right;">Opens</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingQuizzes as $q)
                                        <tr>
                                            <td>{{ $q['name'] }}</td>
                                            <td class="text-muted">{{ $q['category'] }}</td>
                                            <td class="text-muted" style="text-align:right;">{{ $q['opens'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right column --}}
                <div style="display:flex; flex-direction:column; gap:1rem;">

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">Pending member approvals</span></div>
                        <div class="panel-body">
                            @forelse ($pendingApprovals as $user)
                            <div class="disc-item" style="align-items:center; justify-content:space-between;">
                                <span style="font-size:13px;">{{ $user->full_name }}</span>
                                <div style="display:flex; gap:4px; flex-shrink:0;">
                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                        @csrf
                                        <button class="badge badge-teal"
                                            style="border:none; cursor:pointer;">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.decline', $user) }}">
                                        @csrf
                                        <button class="badge badge-red"
                                            style="border:none; cursor:pointer;">Decline</button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No pending
                                approvals.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">Pending group approvals</span></div>
                        <div class="panel-body">
                            @forelse($groups as $g)
                            <div class="disc-item" style="align-items:center; justify-content:space-between;">
                                <div>
                                    <div class="disc-title" style="white-space:normal; font-weight:600;">{{ $g->name }}
                                    </div>
                                    <div class="disc-meta">By: {{ $g->admin->name ?? 'Unknown' }} ·
                                        {{ $g->description }}</div>
                                </div>
                                <div style="display:flex; gap:6px; flex-shrink:0;">
                                    <form method="POST" action="{{ route('admin.groups.approve', $g) }}">
                                        @csrf
                                        <button class="btn btn-sm"
                                            style="background:var(--teal-400); color:#fff; border:none;">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.groups.reject', $g) }}">
                                        @csrf
                                        <button class="btn btn-sm"
                                            style="background:var(--red-400); color:#fff; border:none;">Reject</button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No pending
                                groups.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">Inactivity warnings</span></div>
                        <div class="panel-body">
                            @forelse ($warnedMembers as $user)
                            <div class="activity-item" style="flex-direction:column; align-items:stretch; gap:8px;">
                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                    <span style="font-size:13px;">{{ $user->full_name }}</span>
                                    @if ($user->status === 'blacklisted')
                                    <span class="badge badge-red">Blacklisted</span>
                                    @else
                                    <span class="badge badge-amber">Warning {{ $user->warning_count }} of 2</span>
                                    @endif
                                </div>
                                <div style="display:flex; flex-direction:column; gap:6px;">
                                    @if ($user->status === 'blacklisted')
                                    <form method="POST" action="{{ route('admin.Users.unblacklist', $user) }}"
                                        style="align-self:flex-end;">
                                        @csrf
                                        <button class="btn btn-outline btn-sm">Reinstate</button>
                                    </form>
                                    @else
                                    <details>
                                        <summary class="btn btn-outline btn-sm"
                                            style="display:inline-flex; cursor:pointer;">Warn</summary>
                                        <form method="POST" action="{{ route('admin.users.warn', $user) }}"
                                            style="margin-top:8px; display:flex; flex-direction:column; gap:6px;">
                                            @csrf
                                            <textarea name="message" rows="2" required
                                                placeholder="Describe the rule violation…" class="form-control"
                                                style="font-size:12px;"></textarea>
                                            <button class="btn btn-primary btn-sm" style="width:100%;">Send
                                                warning</button>
                                        </form>
                                    </details>
                                    <div style="display:flex; gap:6px; justify-content:flex-end;">
                                        <form method="POST" action="{{ route('admin.users.logout', $user) }}">
                                            @csrf
                                            <button class="btn btn-outline btn-sm">Log out</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.blacklist', $user) }}"
                                            onsubmit="return confirm('Blacklist {{ $user->full_name }} immediately?');">
                                            @csrf
                                            <button class="btn btn-danger-sm">Blacklist</button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No members
                                currently flagged for inactivity.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-head"><span class="panel-title">ML-recommended trending topics</span></div>
                        <div class="panel-body" style="display:flex; flex-wrap:wrap; gap:8px;">
                            @foreach ($trendingTopics as $t)
                            <span class="badge badge-blue">{{ $t }}</span>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection