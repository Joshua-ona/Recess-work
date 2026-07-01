@extends('layouts.app')

@section('title', 'Groups')

@section('body')
<div style="display:flex; min-height:100vh; ">


       {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ])


    {{-- MAIN --}}
    <div class="dash-main" style="flex:1; width:100%; >

        {{-- HEADER --}}
        <div class="dash-header" style="width:100%;" >
            <div>
                <div class="dash-header-title">Discussion Groups</div>
                <div class="dash-header-sub">Join a group to start discussing</div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/create" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus"></i> New Group
                </a>
            </div>
        </div>

        {{-- BODY --}}
        <div class="dash-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- MY GROUPS --}}
            <div class="dash-header-title" style="margin-bottom:12px;">My Groups</div>
            <div class="stat-grid" style="margin-bottom:30px;">
                @forelse($myGroups as $group)
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users-group"></i> Group
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        {{ $group->name }}
                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        {{ $group->description ?? 'No description' }}
                    </div>
                    <a href="/groups/{{ $group->id }}" 
                       class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-arrow-right"></i> Open Group
                    </a>
                </div>
                @empty
                <p class="text-muted">You haven't joined any groups yet.</p>
                @endforelse
            </div>

            {{-- AVAILABLE GROUPS --}}
            <div class="dash-header-title" style="margin-bottom:12px;">Available Groups</div>
            <div class="stat-grid">
                @forelse($availableGroups as $group)
                <div class="stat-card">
                    <div class="stat-label">
                        <i class="ti ti-users-group"></i> Group
                    </div>
                    <div class="stat-value" style="font-size:17px; margin-bottom:6px;">
                        {{ $group->name }}
                    </div>
                    <div class="text-muted" style="font-size:12px; margin-bottom:12px;">
                        {{ $group->description ?? 'No description' }}
                    </div>
                    <a href="/groups/{{ $group->id }}" 
                       class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                        <i class="ti ti-door-enter"></i> View & Join
                    </a>
                </div>
                @empty
                <p class="text-muted">No new groups available.</p>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection