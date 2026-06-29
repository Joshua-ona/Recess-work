@extends('layouts.app')

@section('title', 'Group Discussions')

@section('body')
<div class="dash-wrap">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-row">
                <div class="sidebar-logo-icon"><i class="ti ti-messages"></i></div>
                <div>
                    <div class="sidebar-logo-name">EduDiscuss</div>
                    <div class="sidebar-logo-sub">E-Discussion Platform</div>
                </div>
            </div>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Main</div>
            <a href="/dashboard" class="sidebar-item"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
            <a href="/groups" class="sidebar-item active"><i class="ti ti-users-group"></i> Groups</a>
            <a href="/discussions" class="sidebar-item"><i class="ti ti-message-circle"></i> Discussions</a>
        </div>
        <div class="sidebar-spacer"></div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-meta">{{ auth()->user()->role }}</div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title">{{ $group->name }}</div>
                <div class="dash-header-sub">Discussions in this group</div>
            </div>
            <div class="dash-header-actions">
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