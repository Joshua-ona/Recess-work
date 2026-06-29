@extends('layouts.app')

@section('title', 'Group Details')

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
                <div class="dash-header-sub">{{ $group->description ?? 'No description' }}</div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/{{ $group->id }}/edit" class="btn btn-primary btn-sm">
                    <i class="ti ti-edit"></i> Edit Group
                </a>
                <form method="POST" action="/groups/{{ $group->id }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline btn-sm" onclick="return confirm('Delete this group?')">
                        <i class="ti ti-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="dash-body">
            <a href="/groups/{{ $group->id }}/discussions" class="btn btn-primary btn-sm">
                <i class="ti ti-message-circle"></i> View Discussions
            </a>
        </div>
    </div>
</div>
@endsection