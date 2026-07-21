@extends('layouts.app')

@section('title', 'Group Discussions')

@section('body')

<div style="display:flex; min-height:100vh;" class="group-page">

    {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount ?? 0,
    ])

    {{-- Main --}}
    <div class="dash-main" style="flex:1;">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    {{ $group->name }}
                </div>
                <div class="dash-header-sub">
                    {{ $group->discussions->count() }}
                    Discussion{{ $group->discussions->count() != 1 ? 's' : '' }}
                </div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/{{ $group->id }}/stats" class="group-btn group-btn-outline group-btn-sm">
                    <i class="ti ti-chart-bar"></i>
                    Statistics
                </a>
                <a href="/groups/{{ $group->id }}/discussions/create" class="group-btn group-btn-primary group-btn-sm">
                    <i class="ti ti-plus"></i>
                    New Discussion
                </a>
            </div>
        </div>

        <div class="group-dash-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="group-search-bar">
                <input
                    type="text"
                    class="form-control"
                    placeholder="🔍 Search discussions..."
                    style="max-width:450px;">
            </div>

            {{-- Discussions - VERTICAL STACK --}}
            <div class="group-discussion-stack">
                @forelse($group->discussions as $discussion)
                    <div class="group-discussion-item">
                        <div class="discussion-header">
                            <h4 class="discussion-title">
                                <a href="/groups/{{ $group->id }}/discussions/{{ $discussion->id }}">
                                    {{ $discussion->title }}
                                </a>
                            </h4>
                            <span class="group-badge group-badge-primary">
                                <i class="ti ti-message-circle"></i>
                                {{ $discussion->replies->count() }} replies
                            </span>
                        </div>

                        <div class="discussion-excerpt">
                            {{ Str::limit($discussion->body, 180) }}
                        </div>

                        <div class="discussion-footer">
                            <div class="discussion-meta">
                                <span>
                                    <i class="ti ti-user-circle"></i>
                                    {{ $discussion->user->first_name ?? 'Unknown' }}
                                </span>
                                <span>
                                    <i class="ti ti-clock"></i>
                                    {{ $discussion->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <a href="/groups/{{ $group->id }}/discussions/{{ $discussion->id }}" class="group-btn group-btn-outline group-btn-sm">
                                Read Discussion <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="group-discussion-item" style="text-align:center; padding:60px;">
                        <i class="ti ti-message-circle" style="font-size:48px; color:var(--gray-300);"></i>
                        <h3 style="margin-top:16px;">No Discussions Yet</h3>
                        <p class="text-muted">Be the first member to start a discussion in this group.</p>
                        <a href="/groups/{{ $group->id }}/discussions/create" class="group-btn group-btn-primary" style="margin-top:20px;">
                            <i class="ti ti-plus"></i>
                            Start Discussion
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection