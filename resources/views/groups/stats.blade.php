@extends('layouts.app')

@section('title', 'Group Statistics')

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
                <div class="dash-header-title">{{ $group->name }} Statistics</div>
                <div class="dash-header-sub">Overview of group activity</div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/{{ $group->id }}/discussions" class="btn btn-outline btn-sm">
                    <i class="ti ti-arrow-left"></i> Back to Group
                </a>
            </div>
        </div>

        <div class="dash-body">
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-message-circle"></i> Discussions</div>
                    <div class="stat-value" style="font-size:28px;">{{ $stats['discussions_count'] }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-messages"></i> Replies</div>
                    <div class="stat-value" style="font-size:28px;">{{ $stats['replies_count'] }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label"><i class="ti ti-clock"></i> Latest Activity</div>
                    <div class="stat-value" style="font-size:15px;">
                        @if($stats['latest_discussion'])
                            {{ $stats['latest_discussion']->title }}
                            <div class="text-muted" style="font-size:11px; margin-top:4px;">
                                {{ $stats['latest_discussion']->created_at->diffForHumans() }}
                            </div>
                        @else
                            No activity yet
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection