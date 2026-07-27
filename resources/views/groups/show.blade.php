@extends('layouts.app')

@section('title', $group->name)

@section('body')
<div style="display:flex; min-height:100vh;" class="group-page">

    {{-- Sidebar --}}
    @include('layouts.sidebar', [
    'role' => 'student',
    'user' => auth()->user(),
    'enrolledCourses' => $enrolledCourses ?? collect(),
    'unreadCount' => $unreadCount ?? 0,
    'notifCount' => $notifCount ?? 0,
    ])

    @php
    $isMember = $group->users()->where('user_id', auth()->id())->exists();
    $creator = $group->users()->first();
    $members = $group->users()->get();
    @endphp

    {{-- Main --}}
    <div class="dash-main" style="flex:1;">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    <i class="ti ti-users-group"></i>
                    {{ $group->name }}
                </div>
                <div class="dash-header-sub">
                    View group information and participate in discussions.
                </div>
            </div>
            <a href="/groups" class="group-btn group-btn-outline group-btn-sm">
                <i class="ti ti-arrow-left"></i>
                Back
            </a>
        </div>

        <div class="group-dash-body">

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            {{-- Group Information --}}
            <div class="group-stat-card" style="padding:30px; margin-bottom:30px;">
                <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                    <div style="
                        width:75px;
                        height:75px;
                        background:var(--primary-bg);
                        border-radius:18px;
                        display:flex;
                        justify-content:center;
                        align-items:center;
                        color:var(--primary);
                        font-size:34px;
                        flex-shrink:0;">
                        <i class="ti ti-users-group"></i>
                    </div>
                    <div>
                        <h2 style="margin-bottom:8px;">{{ $group->name }}</h2>
                        <p class="text-muted">{{ $group->description ?: 'No description available.' }}</p>
                    </div>
                </div>

                <hr style="margin:25px 0;">

                <div style="display:flex; gap:35px; flex-wrap:wrap; color:var(--gray-500);">
                    <div>
                        <strong style="display:block; font-size:20px; color:var(--gray-900);">
                            {{ $group->users->count() }}
                        </strong>
                        Members
                    </div>
                    <div>
                        <strong style="display:block; font-size:20px; color:var(--gray-900);">
                            {{ $group->discussions->count() }}
                        </strong>
                        Discussions
                    </div>
                    <div>
                        <strong style="display:block; font-size:20px; color:var(--gray-900);">
                            {{ $group->created_at->format('d M Y') }}
                        </strong>
                        Created
                    </div>
                </div>
            </div>

            @if(!$isMember)
            {{-- Rules --}}
            <div class="group-stat-card" style="padding:30px;">
                <h3><i class="ti ti-shield-check"></i> Group Rules</h3>
                <ul style="margin-top:20px; line-height:2; padding-left:20px;">
                    <li>Respect every member of the group.</li>
                    <li>Stay on the discussion topic.</li>
                    <li>No spam or offensive content.</li>
                    <li>Support classmates with constructive responses.</li>
                    <li>Repeated violations may result in removal.</li>
                </ul>
                <form method="POST" action="/groups/{{ $group->id }}/join" style="margin-top:25px;">
                    @csrf
                    <button class="group-btn group-btn-primary">
                        <i class="ti ti-user-plus"></i>
                        Join Group
                    </button>
                </form>
            </div>
            @else
            {{-- Member Actions --}}
            <div class="group-stat-card" style="padding:30px; margin-bottom:30px;">
                <h3 style="margin-bottom:10px;">Welcome back 👋</h3>
                <p class="text-muted">You're already a member of this discussion group.</p>
                <div style="margin-top:25px; display:flex; gap:15px; flex-wrap:wrap;">
                    <a href="/groups/{{ $group->id }}/discussions" class="group-btn group-btn-primary">
                        <i class="ti ti-message-circle"></i>
                        View Discussions
                    </a>
                    <a href="/groups" class="group-btn group-btn-outline">
                        Browse Other Groups
                    </a>
                </div>
            </div>
            @endif

            {{-- Members List --}}
            <div class="group-stat-card" style="padding:30px;">
                <h3 style="margin-bottom:20px;"><i class="ti ti-users"></i> Members ({{ $members->count() }})</h3>
                @forelse($members as $member)
                <div
                    style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    <div
                        style="background:var(--primary-bg); border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:600; color:var(--primary); font-size:14px;">
                        {{ strtoupper(substr($member->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:500;">{{ $member->first_name }} {{ $member->last_name }}
                        </div>
                        <div style="font-size:12px; color:#6b7280;">{{ $member->email }}</div>
                    </div>
                    @if($creator && $creator->id === $member->id)
                    <span
                        style="margin-left:auto; background:var(--primary-bg); color:var(--primary); font-size:11px; padding:2px 8px; border-radius:20px; font-weight:500;">
                        Moderator
                    </span>
                    @endif
                </div>
                @empty
                <p style="color:#9ca3af; font-size:14px;">No members yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection