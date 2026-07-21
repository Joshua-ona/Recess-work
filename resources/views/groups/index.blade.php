@extends('layouts.app')

@section('title', 'Discussion Groups')

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

    {{-- Main Content --}}
    <div class="dash-main" style="flex:1; width:100%;">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    <i class="ti ti-users-group"></i>
                    Discussion Groups
                </div>
                <div class="dash-header-sub">
                    Join groups, collaborate with classmates and participate in discussions.
                </div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/create" class="group-btn group-btn-primary group-btn-sm">
                    <i class="ti ti-plus"></i>
                    Create Group
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
                    id="groupSearch"
                    onkeyup="filterGroups()"
                    placeholder="🔍 Search groups..."
                    class="form-control"
                    style="max-width:420px;">
            </div>

            {{-- ================= MY GROUPS ================= --}}
            <div style="margin-bottom:40px;">
                <div class="dash-header-title" style="margin-bottom:20px;">
                    <i class="ti ti-users"></i>
                    My Groups
                </div>

                <div class="group-vertical-stack">
                    @forelse($myGroups as $group)
                        <div class="group-card" data-name="{{ $group->name }}">
                            <div class="group-card-header">
                                <div class="group-card-title">
                                    <div class="avatar">
                                        <i class="ti ti-users-group"></i>
                                    </div>
                                    <h3>{{ $group->name }}</h3>
                                </div>
                                <div class="group-card-meta">
                                    <span class="group-badge group-badge-primary">
                                        <i class="ti ti-users"></i> {{ $group->users->count() }} members
                                    </span>
                                </div>
                            </div>

                            <div class="group-card-body">
                                <p>{{ $group->description ?: 'No description available.' }}</p>
                            </div>

                            <div class="group-card-footer">
                                <div class="stats">
                                    <span>
                                        <i class="ti ti-message-circle"></i>
                                        {{ $group->discussions->count() }} Discussions
                                    </span>
                                    <span>
                                        <i class="ti ti-clock"></i>
                                        {{ $group->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="group-card-actions">
                                    <a href="/groups/{{ $group->id }}" class="group-btn group-btn-primary group-btn-sm">
                                        Open Group <i class="ti ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="group-card" style="text-align:center; padding:60px;">
                            <i class="ti ti-users-group" style="font-size:48px; color:var(--gray-300);"></i>
                            <h3 style="margin-top:16px;">No Joined Groups</h3>
                            <p class="text-muted">Join one of the available groups below to begin participating.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ================= AVAILABLE GROUPS ================= --}}
            <div>
                <div class="dash-header-title" style="margin-bottom:20px;">
                    <i class="ti ti-door-enter"></i>
                    Available Groups
                </div>

                <div class="group-vertical-stack">
                    @forelse($availableGroups as $group)
                        <div class="group-card" data-name="{{ $group->name }}">
                            <div class="group-card-header">
                                <div class="group-card-title">