@extends('layouts.app')

@section('title', 'Group Discussions')

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
                <div class="dash-header-title">{{ $group->name }}</div>
                <div class="dash-header-sub">Discussions in this group</div>
            </div>
            <div class="dash-header-actions">
    <a href="/groups/{{ $group->id }}/stats" class="btn btn-outline btn-sm">
        <i class="ti ti-chart-bar"></i> Statistics
    </a>
    <a href="/groups/{{ $group->id }}/discussions/create" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> New Discussion
    </a>
</div>
        </div>

        <div class="dash-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="stat-grid">
                @forelse($group->discussions as $discussion)
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-message-circle"></i> 
                        {{ $discussion->user->first_name ?? 'Unknown' }}
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        {{ $discussion->title }}
                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        {{ Str::limit($discussion->body, 100) }}
                    </div>
                    <a href="/groups/{{ $group->id }}/discussions/{{ $discussion->id }}" 
                       class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-arrow-right"></i> View Discussion
                    </a>
                </div>
                @empty
                <p class="text-muted">No discussions yet. Start one!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection