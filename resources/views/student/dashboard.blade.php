@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('body')
<div class="dash-wrap">

    {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ])

    {{-- Main --}}
    <div class="dash-main">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                    {{ auth()->user()->first_name ?? auth()->user()->name }}
                </div>
                <div class="dash-header-sub">
                    @if(($unanswered ?? 0) > 0)
                        You have {{ $unanswered }} unanswered {{ Str::plural('question', $unanswered) }}
                    @else
                        All caught up — great work!
                    @endif
                </div>
            </div>
            <div class="dash-header-actions">
                <button class="icon-btn" aria-label="Search">
                    <i class="ti ti-search" aria-hidden="true"></i>
                </button>
                <a href="{{ route('student.discussions.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus" aria-hidden="true"></i> New post
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="icon-btn" aria-label="Log out">
                        <i class="ti ti-logout" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="dash-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Stat cards --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-messages" style="color:var(--purple-600)" aria-hidden="true"></i>
                        Posts made
                    </div>
                    <div class="stat-value">{{ $postCount ?? 0 }}</div>
                    <div class="stat-change text-pos">↑ {{ $postsThisWeek ?? 0 }} this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-heart" style="color:var(--pink-600)" aria-hidden="true"></i>
                        Upvotes received
                    </div>
                    <div class="stat-value">{{ $upvoteCount ?? 0 }}</div>
                    <div class="stat-change text-pos">↑ {{ $upvotesThisWeek ?? 0 }} this week</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-books" style="color:var(--teal-400)" aria-hidden="true"></i>
                        Enrolled courses
                    </div>
                    <div class="stat-value">{{ ($enrolledCourses ?? collect())->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-star" style="color:var(--amber-400)" aria-hidden="true"></i>
                        Reputation
                    </div>
                    <div class="stat-value">{{ number_format($reputation ?? 0) }}</div>
                    <div class="stat-change text-pos">↑ {{ $reputationGain ?? 0 }} pts</div>
                </div>
            </div>

            {{-- Two-column panels --}}
            <div class="panel-grid">

                {{-- Activity --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Recent activity</span>
                        <a href="{{ route('student.discussions.index') }}" class="panel-action">See all</a>
                    </div>
                    <div class="panel-body">
                        @forelse($recentActivity ?? [] as $activity)
                            <div class="activity-item">
                                <div class="activity-dot"
                                     style="background:{{ $activity->color ?? 'var(--purple-600)' }}">
                                </div>
                                <div>
                                    <div class="activity-text">{!! $activity->description !!}</div>
                                    <div class="activity-time">
                                        <span class="badge badge-{{ $activity->badge_color ?? 'purple' }}">
                                            {{ $activity->course_code ?? '' }}
                                        </span>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No activity yet. Start a discussion!</p>
                        @endforelse
                    </div>
                </div>

                {{-- Course discussions --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Course discussions</span>
                        <a href="{{ route('student.discussions.index') }}" class="panel-action">View all</a>
                    </div>
                    <div class="panel-body">
                        @forelse($courseDiscussions ?? [] as $disc)
                            <a href="{{ route('student.discussions.show', $disc->id) }}"
                               style="text-decoration:none;display:block">
                                <div class="disc-item">
                                    <div style="flex:1;min-width:0">
                                        <div class="disc-title">{{ $disc->title }}</div>
                                        <div class="disc-meta">
                                            <span class="badge badge-purple">{{ $disc->course->code ?? 'N/A' }}</span>
                                            {{ $disc->replies_count ?? 0 }} replies
                                        </div>
                                        <div class="progress-bar" style="margin-top:6px">
                                            <div class="progress-fill"
                                                 style="width:{{ min(100, ($disc->engagement_pct ?? 0)) }}%;
                                                        background:{{ $disc->bar_color ?? 'var(--purple-600)' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">
                                No discussions in your courses yet.
                            </p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Trending table --}}
            <div class="full-panel">
                <div class="panel-head">
                    <span class="panel-title">Trending this week</span>
                </div>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Course</th>
                                <th>Replies</th>
                                <th>Upvotes</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trendingDiscussions ?? [] as $disc)
                                <tr>
                                    <td>
                                        <a href="{{ route('student.discussions.show', $disc->id) }}"
                                           style="color:var(--text);text-decoration:none;font-weight:500">
                                            {{ $disc->title }}
                                        </a>
                                    </td>
                                    <td><span class="badge badge-purple">{{ $disc->course->code ?? 'N/A' }}</span></td>
                                    <td>{{ $disc->replies_count ?? 0 }}</td>
                                    <td>{{ $disc->upvotes_count ?? 0 }}</td>
                                    <td>
                                        @if($disc->status === 'open')
                                            <span class="badge badge-green">Open</span>
                                        @elseif($disc->status === 'resolved')
                                            <span class="badge badge-amber">Resolved</span>
                                        @else
                                            <span class="badge badge-red">Closed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:1.5rem">Nothing trending yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- /dash-body --}}
    </div>{{-- /dash-main --}}
</div>{{-- /dash-wrap --}}
@endsection