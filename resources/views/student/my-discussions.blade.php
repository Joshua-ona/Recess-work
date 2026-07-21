@extends('layouts.app')
@section('title', 'My Discussions')
@section('body')
<div style="display:flex; min-height:100vh, flex-direction:column;">
    {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ])
    {{-- MAIN --}}
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title">My Discussions</div>
                <div class="dash-header-sub">Discussions you started or joined</div>
            </div>
        </div>
        <div class="dash-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <h3 style="margin-bottom:12px;">Discussions I started</h3>
            <div class="stat-grid">
                @forelse($startedDiscussions as $discussion)
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users-group"></i>
                        {{ $discussion->group->name ?? 'Unknown group' }}
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        {{ $discussion->title }}
                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        {{ Str::limit($discussion->body, 100) }}
                    </div>
                    <a href="/groups/{{ $discussion->group_id }}/discussions/{{ $discussion->id }}"
                       class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-arrow-right"></i> View Discussion
                    </a>
                </div>
                @empty
                <p class="text-muted">You haven't started any discussions yet.</p>
                @endforelse
            </div>

            <h3 style="margin:24px 0 12px;">Discussions I replied to</h3>
            <div class="stat-grid">
                @forelse($repliedDiscussions as $discussion)
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users-group"></i>
                        {{ $discussion->group->name ?? 'Unknown group' }}
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        {{ $discussion->title }}
                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        Started by {{ $discussion->user->first_name ?? 'Unknown' }}
                    </div>
                    <a href="/groups/{{ $discussion->group_id }}/discussions/{{ $discussion->id }}"
                       class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-arrow-right"></i> View Discussion
                    </a>
                </div>
                @empty
                <p class="text-muted">You haven't replied to any discussions yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
