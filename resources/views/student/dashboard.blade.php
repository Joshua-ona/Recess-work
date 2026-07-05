@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('body')

<div class="dash-wrap">



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
                    @if (($unanswered ?? 0) > 0)
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
            <div class="dash-main">
                <div class="dash-header">...</div>
                <div class="dash-body">
                    @if($activeGroup)
                    @include('student.groups.show',
                    [
                    'group' => $activeGroup,
                    'admin' => $admin,
                    'members' => $members,
                    'messages' => $messages,
                    'role' => 'student',
                    'user' => auth()->user(),
                    'enrolledCourses' => $enrolledCourses ?? collect(),
                    'unreadCount' => $unreadCount ?? 0,
                    'notifCount' => $notifCount ?? 0,
                    ])

                    @else
                    @include('student.groups.index',[
                    'myGroups' => $myGroups,
                    'discoverGroups' => $discoverGroups
                    ])
                    @endif
                </div>
            </div>

            @if (session('success'))
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
                    <div class="stat-value">{{ $upcomingQuiz ?? 0 }}</div>
                    <div class="stat-change text-pos">↑ {{ $upComingQuizThisWeek ?? 0 }} this week</div>
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
                        Participation Score
                    </div>
                    <div class="stat-value">{{ number_format($ParticipationScore ?? 0) }}</div>
                    <div class="stat-change text-pos">↑ {{ $ParticipationGain ?? 0 }} pts</div>
                </div>
            </div>


            <div class="panel-grid" style="margin: 24px 0;">
                {{-- PANEL 1: My Groups --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title"><i class="titi-users"></i> My Groups</span>
                    </div>
                    <div class="panel-body">
                        @if($myGroups->isEmpty())
                        <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No groups
                            yet.Request to create one.</p>
                        @else
                        @foreach($myGroups as $g)
                        <div class="disc-item">
                            <div
                                style="width:8px; height:8px; border-radius:50%; background:{{ $g->status == 'approved' ? 'var(--green-600)' : 'var(--amber-600)' }};">
                            </div>
                            <div style="flex:1;">
                                <a href="{{route('student.groups.show', $g)}}">
                                    <div class="disc-title">{{ $g->name }}</div>
                                </a>
                            </div>

                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>



                {{-- PANEL 1: All Groups --}}
                <div class="panel">
                    <div class="panel-head"> <span class="panel-title"><i class="titi-users"></i> Browse All
                            Groups</span>
                    </div>
                    <div class="panel-body">
                        @forelse($browseGroups as $g)
                        <div class="disc-item">
                            <div style="width:8px; height:8px; border-radius:50%; background:var(--green-600)';">
                            </div>
                            <div style="flex:1;">
                                <div class="disc-title">{{ $g->name }}</div>
                                <div class="disc-meta">
                                    <span class="badge badge-green">approved</span>
                                </div>

                                <form method="POST" action="{{ route('student.groups.join', $g) }}" class="disc-item"
                                    style="display:flex;justify-content:space-between;align-items:center;">
                                    @csrf
                                    <button type="submit" class="panel-action"
                                        style="border:none;background:none; color:var(--blue-600);cursor:pointer;">Join</button>
                                </form>

                            </div>
                            @empty
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No
                                Approved
                                groups to join.</p>
                            @endforelse
                        </div>
                    </div>


                    {{-- PANEL 2: Create / Join Groups --}}
                    <div class="panel">
                        <div class="panel-head">
                            <span class="panel-title"><i class="titi-circle-plus"></i> Create / Join Groups</ span>
                        </div>
                        <div class="panel-body">
                            @if(session('success'))
                            <p
                                style="font-size:13px;color:#065f46;background:#ecfdf5;padding:8px;border-radius:8px;margin-bottom:8px;">
                                {{ session('success') }}</p>
                            @endif
                            <form method="POST" action="{{ route('student.groups.store') }}"
                                style="margin-bottom: 16px;">
                                @csrf
                                <input name="name" value="{{ old('name') }}" placeholder="Groupname e.g. Java"
                                    style="width:100%; padding:8px; border:1px solid #e5e7eb;border-radius:8px; font-size:14px;">
                                @error('name')
                                <p style="font-size:12px;color:#dc2626; margin-top:4px;">{{ $message }}</p>
                                @enderror
                                <button class="btn btn-primary" style="width:100%; margin-top:8px;">Request
                                    Group</button>
                                <p style="font-size:11px;color:var(--muted); margin-top:4px;">Needs admin approval.
                                </p>
                            </form>

                        </div>
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
                            <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">No
                                activity
                                yet. Start a discussion!</p>
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
                                            <div class="progress-fill" style="width:{{ min(100, $disc->engagement_pct ?? 0) }}%;
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
                                    <td><span class="badge badge-purple">{{ $disc->course->code ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $disc->replies_count ?? 0 }}</td>
                                    <td>{{ $disc->upvotes_count ?? 0 }}</td>
                                    <td>
                                        @if ($disc->status === 'open')
                                        <span class="badge badge-green">Open</span>
                                        @elseif($disc->status === 'resolved')
                                        <span class="badge badge-amber">Resolved</span>
                                        @else
                                        <span class="badge badge-red">Closed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--muted);padding:1.5rem">
                                        Nothing trending yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- /dash-body --}}
        </div>{{-- /dash-main --}}
    </div>{{-- /dash-wrap --}}
    @endsection