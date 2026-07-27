@extends('layouts.app')
@section('title', 'Lecturer Dashboard')

@section('body')
<div class="dash-wrap">

    {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'         => 'lecturer',
        'user'         => auth()->user(),
        'myCourses'    => $myCourses   ?? collect(),
        'flaggedCount' => $flaggedCount ?? 0,
    ])

    {{-- Main --}}
    <div class="dash-main">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                    Dr. {{ auth()->user()->last_name ?? auth()->user()->name }}
                </div>
                <div class="dash-header-sub">
                    @if(($flaggedCount ?? 0) > 0)
                        {{ $flaggedCount }} flagged {{ Str::plural('post', $flaggedCount) }} need{{ $flaggedCount === 1 ? 's' : '' }} your attention
                    @else
                        All clear — no flagged posts
                    @endif
                </div>
            </div>
            <div class="dash-header-actions">
                <button class="icon-btn" aria-label="Search">
                    <i class="ti ti-search" aria-hidden="true"></i>
                </button>
                <a href="{{ route('lecturer.announcements.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-speakerphone" aria-hidden="true"></i> Post announcement
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
                        <i class="ti ti-users" style="color:#6C63FF" aria-hidden="true"></i>
                        My students
                    </div>
                    <div class="stat-value">{{ $studentCount ?? 0 }}</div>
                    <div class="stat-change text-muted">Across {{ ($myCourses ?? collect())->count() }} courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-messages" style="color:#00B8D4" aria-hidden="true"></i>
                        Threads this week
                    </div>
                    <div class="stat-value">{{ $threadsThisWeek ?? 0 }}</div>
                    @php $threadDelta = ($threadsThisWeek ?? 0) - ($threadsLastWeek ?? 0); @endphp
                    <div class="stat-change {{ $threadDelta >= 0 ? 'text-pos' : 'text-neg' }}">
                        {{ $threadDelta >= 0 ? '↑' : '↓' }} {{ abs($threadDelta) }} vs last week
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-message-reply" style="color:#FFB300" aria-hidden="true"></i>
                        Unanswered
                    </div>
                    <div class="stat-value">{{ $unansweredCount ?? 0 }}</div>
                    @if(($unansweredCount ?? 0) > 0)
                        <div class="stat-change text-neg">Needs response</div>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-star" style="color:#FF4081" aria-hidden="true"></i>
                        Avg. satisfaction
                    </div>
                    <div class="stat-value">{{ number_format($avgSatisfaction ?? 0, 1) }}</div>
                    @php $satDelta = ($avgSatisfaction ?? 0) - ($prevSatisfaction ?? 0); @endphp
                    <div class="stat-change {{ $satDelta >= 0 ? 'text-pos' : 'text-neg' }}">
                        {{ $satDelta >= 0 ? '↑' : '↓' }} {{ number_format(abs($satDelta), 1) }} this month
                    </div>
                </div>
            </div>

            {{-- Two-column panels --}}
            <div class="panel-grid">

                {{-- Unanswered questions --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Unanswered questions</span>
                        <a href="{{ route('lecturer.discussions.index') }}" class="panel-action">Reply to all</a>
                    </div>
                    <div class="panel-body">
                        @forelse($unansweredDiscussions ?? [] as $disc)
                            <a href="{{ route('lecturer.discussions.show', $disc->id) }}"
                               style="text-decoration:none;display:block">
                                <div class="disc-item">
                                    <div class="disc-avatar" style="background:#E8EAF6;color:#3949AB">
                                        {{ strtoupper(substr($disc->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($disc->user->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div class="disc-title">{{ $disc->title }}</div>
                                        <div class="disc-meta">
                                            <span class="badge badge-blue">{{ $disc->course->code ?? 'N/A' }}</span>
                                            Asked {{ $disc->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p style="font-size:13px;color:#9E9E9E;text-align:center;padding:1rem 0">
                                No unanswered questions — well done!
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Engagement per course --}}
                <div class="panel">
                    <div class="panel-head">
                        <span class="panel-title">Student engagement</span>
                        <a href="{{ route('lecturer.engagement') }}" class="panel-action">Details</a>
                    </div>
                    <div class="panel-body">
                        @forelse($courseEngagement ?? [] as $item)
                            <div class="progress-wrap">
                                <div class="progress-label">
                                    <span>{{ $item['course_code'] }} — {{ $item['course_name'] }}</span>
                                    <span>{{ $item['pct'] }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                         style="width:{{ $item['pct'] }}%;background:{{ $item['color'] ?? '#6C63FF' }}">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="font-size:13px;color:#9E9E9E;text-align:center;padding:1rem 0">No engagement data yet.</p>
                        @endforelse

                        @if(!empty($overallResponseRate))
                            <div class="progress-wrap" style="margin-top:8px;padding-top:8px;border-top:1px solid #E0E0E0">
                                <div class="progress-label">
                                    <span style="font-weight:500">Overall response rate</span>
                                    <span>{{ $overallResponseRate }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                         style="width:{{ $overallResponseRate }}%;background:#FF4081">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Discussion threads table --}}
            <div class="full-panel">
                <div class="panel-head">
                    <span class="panel-title">Discussion threads</span>
                    <a href="{{ route('lecturer.discussions.create') }}" class="panel-action">+ Create thread</a>
                </div>
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Course</th>
                                <th>Students engaged</th>
                                <th>Replies</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($threads ?? [] as $thread)
                                <tr>
                                    <td>
                                        <a href="{{ route('lecturer.discussions.show', $thread->id) }}"
                                           style="color:#212121;text-decoration:none;font-weight:500">
                                            {{ $thread->title }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $thread->badge_color ?? 'blue' }}">
                                            {{ $thread->course->code ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $thread->engaged_students ?? 0 }}/{{ $thread->total_students ?? 0 }}</td>
                                    <td>{{ $thread->replies_count ?? 0 }}</td>
                                    <td>
                                        @if($thread->status === 'open')
                                            <span class="badge badge-green">Open</span>
                                        @elseif($thread->status === 'closed')
                                            <span class="badge badge-amber">Closed</span>
                                        @else
                                            <span class="badge badge-red">Archived</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('lecturer.discussions.edit', $thread->id) }}"
                                           style="font-size:12px;color:#6C63FF;text-decoration:none;">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;color:#9E9E9E;padding:1.5rem">No threads yet. Create one to get started.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($threads) && $threads->hasPages())
                    <div style="padding:12px 1rem;border-top:1px solid #E0E0E0">
                        {{ $threads->links() }}
                    </div>
                @endif
            </div>

        </div>{{-- /dash-body --}}
    </div>{{-- /dash-main --}}
</div>{{-- /dash-wrap --}}
@endsection