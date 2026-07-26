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

        {{-- Uniform 4-Column Stat Cards Grid --}}
        <div class="stat-grid"
            style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">

            {{-- Card 1: Posts Made --}}
            <div class="stat-card card shadow-sm p-3 bg-white rounded">
                <div class="stat-label text-muted mb-1" style="font-size: 14px;">Posts made</div>
                <div class="stat-value fw-bold fs-3" style="color: #111;">{{ $postCount ?? 0 }}</div>
            </div>

            {{-- Card 2: Quizzes --}}
            <div class="stat-card card shadow-sm p-3 bg-white rounded">
                <div class="stat-label text-muted mb-1" style="font-size: 14px;">Quizzes</div>
                <div class="stat-value fw-bold fs-3" style="color: #111;">{{ $quizCount ?? 0 }}</div>
            </div>

            {{-- Card 3: Groups --}}
            <div class="stat-card card shadow-sm p-3 bg-white rounded">
                <div class="stat-label text-muted mb-1" style="font-size: 14px;">Groups Joined</div>
                <div class="stat-value fw-bold fs-3" style="color: #111;">{{ $groupCount ?? ($myGroups->count() ?? 0) }}
                </div>
            </div>

            {{-- Card 4: Participation Score --}}
            <div class="stat-card card shadow-sm p-3 bg-white rounded">
                <div class="stat-label text-muted mb-1" style="font-size: 14px;">Participation Score</div>
                <div class="stat-value fw-bold fs-3 text-primary" id="participation-score">{{ $score }}/100</div>
                <div class="progress mb-2 mt-1" style="height:6px;">
                    <div id="score-progress" class="progress-bar bg-primary" style="width: {{ $score }}%"></div>
                </div>
                <span id="score-badge">
                    @if($score >= 70)
                    <span class="badge bg-success">Leader</span>
                    @elseif($score >= 30)
                    <span class="badge bg-primary">Contributor</span>
                    @else
                    <span class="badge bg-secondary">Newcomer</span>
                    @endif
                </span>
                <small class="d-block text-muted mt-1" style="font-size: 11px;">Updated daily</small>
            </div>

        </div>

        {{-- 3-COLUMN TOP SECTION --}}
        <div class="panel-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin: 24px 0;">
            {{-- PANEL 1: My Groups --}}
            <div class="panel card p-3 bg-white shadow-sm">
                <div class="panel-head mb-3">
                    <span class="panel-title fw-bold"><i class="ti ti-users"></i> My Groups</span>
                </div>
                <div class="panel-body">
                    @if($myGroups->isEmpty())
                    <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No groups yet.
                        Request to create one.</p>
                    @else
                    @foreach($myGroups as $g)
                    <div class="disc-item d-flex align-items-center gap-2 mb-2">
                        <div
                            style="width:8px; height:8px; border-radius:50%; background:{{ $g->status == 'approved' ? 'var(--green-600, #16a34a)' : 'var(--amber-600, #d97706)' }};">
                        </div>
                        <div style="flex:1;">
                            <a href="{{ route('student.groups.show', $g) }}" style="text-decoration:none;">
                                <div class="disc-title" style="color: #2563eb; font-weight: 500;">{{ $g->name }}</div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- PANEL 2: Browse All Groups --}}
            <div class="panel card p-3 bg-white shadow-sm">
                <div class="panel-head mb-3">
                    <span class="panel-title fw-bold"><i class="ti ti-grid-dots"></i> Browse All Groups</span>
                </div>
                <div class="panel-body">
                    @php
                    $myGroupIds = $myGroups->pluck('id')->toArray();
                    $browseFiltered = $browseGroups->unique('id')->whereNotIn('id', $myGroupIds);
                    @endphp

                    @forelse($browseFiltered as $g)
                    <div class="disc-item d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="width:8px; height:8px; border-radius:50%; background:var(--green-600, #16a34a);">
                            </div>
                            <div>
                                <div class="disc-title fw-medium">{{ $g->name }}</div>
                                <div class="disc-meta"><span class="badge bg-success"
                                        style="font-size: 10px;">approved</span></div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('student.groups.join', $g) }}">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm btn-primary px-3 py-1 shadow-sm rounded-pill fw-semibold d-inline-flex align-items-center gap-1"
                                style="font-size: 12px; transition: all 0.2s ease-in-out;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                                    class="bi bi-person-plus-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                    <path fill-rule="evenodd"
                                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                                </svg>
                                Join Group
                            </button>
                        </form>
                    </div>
                    @empty
                    <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0;">No other groups to
                        browse.</p>
                    @endforelse
                </div>
            </div>

            {{-- PANEL 3: Create / Join Groups --}}
            <div class="panel card p-3 bg-white shadow-sm">
                <div class="panel-head mb-3">
                    <span class="panel-title fw-bold"><i class="ti ti-circle-plus"></i> Create / Join Groups</span>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('student.groups.store') }}">
                        @csrf
                        <input name="name" value="{{ old('name') }}" placeholder="Group name e.g. Java"
                            style="width:100%; padding:8px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; margin-bottom: 8px;">
                        @error('name') <p style="font-size:12px;color:#dc2626; margin-top:4px;">{{ $message }}</p>
                        @enderror
                        <button class="btn btn-primary w-100 btn-sm">Request Group</button>
                        <p style="font-size:11px;color:var(--muted); margin-top:4px;">Needs admin approval.</p>
                    </form>
                </div>
            </div>
        </div>

        {{-- FULL WIDTH: AI RECOMMENDED GROUPS --}}
        <div class="full-panel card p-3 bg-white shadow-sm">
            <div class="panel-head mb-3">
                <span class="panel-title fw-bold"><i class="ti ti-compass"></i> Groups You Might Like</span>
            </div>
            <div class="panel-body">
                @if(!empty($recommendedGroups) && count($recommendedGroups) > 0)
                @foreach($recommendedGroups as $group)
                <div class="card mb-3 w-100 border p-3">
                    <div class="card-body d-flex justify-content-between align-items-center p-0">
                        <div style="flex:1;">
                            <h5 class="mb-1 fw-bold">{{ $group['name'] }}</h5>
                            <p class="text-muted mb-2" style="font-size:13px;">
                                {{ Str::limit($group['description'] ?? '', 120) }}
                            </p>
                            <div class="d-flex gap-2">
                                <span
                                    class="badge bg-info text-dark">{{ $group['reason'] ?? 'Recommended for you' }}</span>
                                <span class="badge bg-secondary">{{ $group['discussions_count'] ?? 0 }}
                                    discussions</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success fs-6 mb-2">{{ $group['score'] ?? 0 }}% match</span>
                            <br>
                            <form method="POST" action="{{ route('student.groups.join', $group['id']) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary mt-2">
                                    <i class="ti ti-user-plus"></i> Join
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <p style="text-align:center;color:var(--muted);padding:1.5rem 0;">
                    <i class="ti ti-robot fs-2"></i><br>
                    No recommendations yet. <br>
                    <b><small>Post more in groups to get recommended</small></b>
                </p>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    function updateScore() {
        fetch("{{ url('/student/score') }}")
            .then(response => response.json())
            .then(data => {
                let score = data.score ?? 0;
                document.getElementById('participation-score').innerText = score + "/100";
                document.getElementById('score-progress').style.width = score + "%";
                let badge = document.getElementById('score-badge');

                if (score >= 70) {
                    badge.innerHTML = '<span class="badge bg-success">Leader</span>';
                } else if (score >= 30) {
                    badge.innerHTML = '<span class="badge bg-primary">Contributor</span>';
                } else {
                    badge.innerHTML = '<span class="badge bg-secondary">Newcomer</span>';
                }
            })
            .catch(error => {
                console.error("Score update failed:", error);
            });
    }

    // Refresh score periodically if needed
    setInterval(updateScore, 120000);
});
</script>
@endpush

@endsection