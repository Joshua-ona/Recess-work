@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('body')

<div class="dash-wrap">


    @include('layouts.sidebar', [
    'role' => 'student',
    'user' => auth()->user(),
    'enrolledCourses' => $enrolledCourses ?? collect(),
    'unreadCount' => $unreadCount ?? 0,
    'notifCount' => $notifCount ?? 0,

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


        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Stat cards --}}
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--purple-50);">
                        <i class="ti ti-messages" style="color:var(--purple-600)" aria-hidden="true"></i>
                    </span>
                    Posts made
                </div>
                <div class="stat-value">{{ $postCount ?? 0 }}</div>
                <div class="stat-change text-pos">↑ {{ $postsThisWeek ?? 0 }} this week</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--pink-50);">
                        <i class="ti ti-heart" style="color:var(--pink-600)" aria-hidden="true"></i>
                    </span>
                    Upvotes received
                </div>
                <div class="stat-value">{{ $upcomingQuiz ?? 0 }}</div>
                <div class="stat-change text-pos">↑ {{ $upComingQuizThisWeek ?? 0 }} this week</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">
                    <span class="stat-icon" style="background:var(--teal-50);">
                        <i class="ti ti-books" style="color:var(--teal-400)" aria-hidden="true"></i>
                    </span>
                    Enrolled courses
                </div>
                <div class="stat-value">{{ ($enrolledCourses ?? collect())->count() }}</div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Participation Score</h6>
                        <h2 class="fw-bold text-primary">{{ $score }}/100</h2>

                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $score }}%"></div>
                        </div>

                        @if($score >= 70) <span class="badge bg-success">Leader</span>
                        @elseif($score >= 30) <span class="badge bg-primary">Contributor</span>
                        @else <span class="badge bg-secondary">Newcomer</span>
                        @endif
                        <small class="d-block text-muted">Updated daily</small>
                    </div>
                </div>
            </div>
        </div>


        <!-- <div class="panel-grid" style="grid-template-columns: 1fr 1fr 1fr; margin: 24px 0;">
            {{-- PANEL 1: My Groups --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title"><i class="ti ti-users"></i> My Groups</span>
                </div>
                <div class="panel-body">
                    @if($myGroups->isEmpty())
                    <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No groups
                        yet. Request to create one.</p>
                    @else
                    @foreach($myGroups as $g)
                    <div class="disc-item">
                        <div
                            style="width:8px; height:8px; border-radius:50%; margin-top:4px; background:{{ $g->status == 'approved' ? 'var(--green-600)' : 'var(--amber-600)' }};">
                        </div>
                        <div style="flex:1;">
                            <a href="{{route('student.groups.show', $g)}}" style="text-decoration:none;">
                                <div class="disc-title">{{ $g->name }}</div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- PANEL 2: All Groups --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title"><i class="ti ti-users"></i> Browse All Groups</span>
                </div>
                <div class="panel-body">
                    @forelse($browseGroups as $g)
                    <div class="disc-item">
                        <div
                            style="width:8px; height:8px; border-radius:50%; margin-top:4px; background:var(--green-600);">
                        </div>
                        <div style="flex:1;display:flex;justify-content:space-between;align-items:center;gap:8px;">
                            <div>
                                <div class="disc-title">{{ $g->name }}</div>
                                <div class="disc-meta">
                                    <span class="badge badge-green">approved</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('student.groups.join', $g) }}">
                                @csrf
                                <button type="submit" class="panel-action"
                                    style="border:none;background:none; color:var(--blue-600);cursor:pointer;">Join</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No
                        approved groups to join.</p>
                    @endforelse
                </div>
            </div>

            {{-- PANEL 3: Create / Join Groups --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title"><i class="ti ti-circle-plus"></i> Create / Join Groups</span>
                </div>
                <div class="panel-body">
                    @if(session('success'))
                    <p
                        style="font-size:13px;color:#065f46;background:#ecfdf5;padding:8px;border-radius:8px;margin-bottom:8px;">
                        {{ session('success') }}</p>
                    @endif
                    <form method="POST" action="{{ route('student.groups.store') }}" style="margin-bottom: 16px;">
                        @csrf
                        <input name="name" value="{{ old('name') }}" placeholder="Group name e.g. Java"
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
                        <div class="activity-dot" style="background:{{ $activity->color ?? 'var(--purple-600)' }}">
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


        {{-- NEW: RECOMMENDED GROUPS YOU'RE NOT IN --}}
        @if(!empty($recommendedGroups) && count($recommendedGroups) > 0)
        <div class="full-panel">
            <div class="panel-head">
                <span class="panel-title"><i class="ti ti-compass"></i> Groups You Might Like</span>
                <span class="panel-action">Based on your activity</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    @foreach($recommendedGroups as $group)
                    <div class="card mb-3 w-100">
                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div style="flex:1;">
                                <h5 class="mb-1">{{ $group['name'] }}</h5>
                                <p class="text-muted mb-2" style="font-size:13px;">
                                    {{ Str::limit($group['description'], 120) }}
                                </p>
                                <div class="d-flex gap-2">
                                    <span class="badge badge-info">{{ $group['reason'] }}</span>
                                    <span class="badge badge-purple">{{ $group['discussion_count'] }} discussions</span>
                                </div>
                            </div>

                            <div class="text-end">
                                <span class="badge badge-green fs-6 mb-2">{{ $group['score'] }}% match</span>
                                <br>
                                <form method="POST" action="{{ route('student.groups.join', $group['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ti ti-user-plus"></i> Join
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div> -->


        {{-- 3-COLUMN TOP SECTION --}}
        <div class="panel-grid" style="grid-template-columns: 1fr 1fr 1fr; margin: 24px 0;">
            {{-- PANEL 1: My Groups --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title"><i class="ti ti-users"></i> My Groups</span>
                </div>
                <div class="panel-body">
                    @if($myGroups->isEmpty())
                    <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No groups yet.
                        Request to create one.</p>
                    @else
                    @foreach($myGroups as $g)
                    <div class="disc-item">
                        <div
                            style="width:8px; height:8px; border-radius:50%; margin-top:4px; background:{{ $g->status == 'approved' ? 'var(--green-600)' : 'var(--amber-600)' }};">
                        </div>
                        <div style="flex:1;">
                            <a href="{{route('student.groups.show', $g)}}" style="text-decoration:none;">
                                <div class="disc-title">{{ $g->name }}</div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- PANEL 2: Browse All Groups - FIXED DUPLICATES --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title"><i class="ti ti-grid-dots"></i> Browse All Groups</span>
                </div>
                <div class="panel-body">
                    @php
                    $myGroupIds = $myGroups->pluck('id')->toArray();
                    $browseFiltered = $browseGroups->unique('id')->whereNotIn('id', $myGroupIds);
                    @endphp

                    @forelse($browseFiltered as $g)
                    <div class="disc-item">
                        <div
                            style="width:8px; height:8px; border-radius:50%; margin-top:4px; background:var(--green-600);">
                        </div>
                        <div style="flex:1;display:flex;justify-content:space-between;align-items:center;gap:8px;">
                            <div>
                                <div class="disc-title">{{ $g->name }}</div>
                                <div class="disc-meta"><span class="badge badge-green">approved</span></div>
                            </div>
                            <form method="POST" action="{{ route('student.groups.join', $g) }}">
                                @csrf
                                <button type="submit" class="panel-action"
                                    style="border:none;background:none; color:var(--blue-600);cursor:pointer;">Join</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No other groups to
                        browse.</p>
                    @endforelse
                </div>
            </div>

            {{-- PANEL 3: Create / Join Groups --}}
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title"><i class="ti ti-circle-plus"></i> Create / Join Groups</span>
                </div>
                <div class="panel-body">
                    @if(session('success'))
                    <p
                        style="font-size:13px;color:#065f46;background:#ecfdf5;padding:8px;border-radius:8px;margin-bottom:8px;">
                        {{ session('success') }}</p>
                    @endif
                    <form method="POST" action="{{ route('student.groups.store') }}" style="margin-bottom: 16px;">
                        @csrf
                        <input name="name" value="{{ old('name') }}" placeholder="Group name e.g. Java"
                            style="width:100%; padding:8px; border:1px solid #e5e7eb;border-radius:8px; font-size:14px;">
                        @error('name') <p style="font-size:12px;color:#dc2626; margin-top:4px;">{{ $message }}</p>
                        @enderror
                        <button class="btn btn-primary" style="width:100%; margin-top:8px;">Request Group</button>
                        <p style="font-size:11px;color:var(--muted); margin-top:4px;">Needs admin approval.</p>
                    </form>
                </div>
            </div>
        </div>


        {{-- FULL WIDTH: AI RECOMMENDED GROUPS YOU'RE NOT IN --}}
        <div class="full-panel">
            <div class="panel-head">
                <span class="panel-title"><i class="ti ti-compass"></i> Groups You Might Like</span>
                <span class="panel-action">75%+ match only</span>
            </div>
            <div class="panel-body">
                @if(!empty($recommendedGroups) && count($recommendedGroups) > 0)
                @foreach($recommendedGroups as $group)
                <div class="card mb-3 w-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div style="flex:1;">
                            <h5 class="mb-1">{{ $group['name'] }}</h5>
                            <p class="text-muted mb-2" style="font-size:13px;">
                                {{ Str::limit($group['description'], 120) }}
                            </p>
                            <div class="d-flex gap-2">
                                <span class="badge badge-info">{{ $group['reason'] }}</span>
                                <span class="badge badge-purple">{{ $group['discussion_count'] }} discussions</span>
                            </div>
                        </div>

                        <div class="text-end">
                            <span class="badge badge-green fs-6 mb-2">{{ $group['score'] }}% match</span>
                            <br>
                            <form method="POST" action="{{ route('student.groups.join', $group['id']) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ti ti-user-plus"></i> Join
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <p style="text-align:center;color:var(--muted);padding:1.5rem 0;">
                    <i class="ti ti-robot"></i><br>
                    No strong matches yet. <br>
                    <small>Post more in discussions to get 75%+ recommendations</small>
                </p>
                @endif
            </div>
        </div>


    </div>{{-- /dash-body --}}
</div>{{-- /dash-main --}}
</div>{{-- /dash-wrap --}}
@endsection