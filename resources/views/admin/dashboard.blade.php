@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('body')
<div class="dash-wrap">

    {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'        => 'admin',
        'user'        => auth()->user(),
        'userCount'   => $userCount   ?? 0,
        'openReports' => $openReports ?? 0,
    ])

    {{-- Main --}}
    <div class="dash-main">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">Dashboard</div>
                <div class="dash-header-sub">{{ now()->format('l, j F Y') }}</div>
            </div>
            <div class="dash-header-actions">
                <button class="icon-btn" aria-label="Search">
                    <i class="ti ti-search" aria-hidden="true"></i>
                </button>
                <button class="icon-btn" aria-label="Notifications">
                    <i class="ti ti-bell" aria-hidden="true"></i>
                </button>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="icon-btn" aria-label="Log out">
                        <i class="ti ti-logout" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="dash-body">

            {{-- Flash --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Stat cards --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users" style="color:var(--purple-600)" aria-hidden="true"></i>
                        Total users
                    </div>
                    <div class="stat-value">{{ number_format($userCount ?? 0) }}</div>
                    <div class="stat-change text-pos">↑ {{ $newUsersThisWeek ?? 0 }} this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-messages" style="color:var(--teal-400)" aria-hidden="true"></i>
                        Discussions
                    </div>
                    <div class="stat-value">{{ number_format($discussionCount ?? 0) }}</div>
                    <div class="stat-change text-pos">↑ {{ $discussionsToday ?? 0 }} today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-books" style="color:var(--amber-400)" aria-hidden="true"></i>
                        Courses
                    </div>
                    <div class="stat-value">{{ $courseCount ?? 0 }}</div>
                    <div class="stat-change text-muted">Across {{ $deptCount ?? 0 }} departments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-flag" style="color:var(--red-400)" aria-hidden="true"></i>
                        Open reports
                    </div>
                    <div class="stat-value">{{ $openReports ?? 0 }}</div>
                    @if(($openReports ?? 0) > 0)
                        <div class="stat-change text-neg">Needs review</div>
                    @endif
                </div>
            </div>

            {{-- Two-column panels --}}
            <div class="panel-grid">

                {{-- Recent discussions --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Recent discussions</span>
                        <a href="{{ route('admin.discussions') }}" class="panel-action">View all</a>
                    </div>
                    <div class="panel-body">
                        @forelse($recentDiscussions ?? [] as $disc)
                            <div class="disc-item">
                                <div class="disc-avatar" style="background:var(--purple-50);color:var(--purple-800)">
                                    {{ strtoupper(substr($disc->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($disc->user->last_name ?? '', 0, 1)) }}
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div class="disc-title">{{ $disc->title }}</div>
                                    <div class="disc-meta">
                                        <span class="badge badge-purple">{{ $disc->course->code ?? 'N/A' }}</span>
                                        {{ $disc->replies_count ?? 0 }} replies ·
                                        {{ $disc->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No discussions yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Activity feed --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Activity feed</span>
                    </div>
                    <div class="panel-body">
                        @forelse($activities ?? [] as $activity)
                            <div class="activity-item">
                                <div class="activity-dot"></div>
                                <div>
                                    <div class="activity-text">{!! $activity->description !!}</div>
                                    <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No recent activity.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- User table --}}
            <div class="full-panel">
                <div class="panel-head">
                    <span class="panel-title">User accounts</span>
                    <a href="{{ route('admin.users.create') }}" class="panel-action">+ Add user</a>
                </div>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Last active</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users ?? [] as $user)
                                <tr>
                                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge badge-purple">Admin</span>
                                        @elseif($user->role === 'lecturer')
                                            <span class="badge badge-teal">Lecturer</span>
                                        @else
                                            <span class="badge badge-blue">Student</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->department->name ?? '—' }}</td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge badge-green">Active</span>
                                        @elseif($user->status === 'pending')
                                            <span class="badge badge-amber">Pending</span>
                                        @else
                                            <span class="badge badge-red">Suspended</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : '—' }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                           style="font-size:12px;color:var(--purple-600);text-decoration:none;">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:1.5rem">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($users) && $users->hasPages())
                    <div style="padding:12px 1rem;border-top:var(--border)">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>{{-- /dash-body --}}
    </div>{{-- /dash-main --}}
</div>{{-- /dash-wrap --}}
@endsection