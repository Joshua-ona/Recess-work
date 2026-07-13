@extends('layouts.app')

@section('title', 'show')

@section('body')
<div style="display:flex; min-height:100vh;">

    {{-- Sidebar --}}
    @include('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ])

    {{-- MAIN --}}
    <div style="flex:1; padding:24px; background:#f9fafb;">

        @if(session('success'))
            <div style="background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        @php
            $isMember = $group->users()->where('user_id', auth()->id())->exists();
            $creator = $group->users()->first();
            $members = $group->users()->get();
        @endphp

        {{-- Group Header --}}
        <div style="background:#fff; border-radius:12px; padding:24px; margin-bottom:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
                <div style="background:#ede9fe; border-radius:12px; width:56px; height:56px; display:flex; align-items:center; justify-content:center;">
                    <i class="ti ti-users-group" style="color:#4f46e5; font-size:28px;"></i>
                </div>
                <div>
                    <div style="font-size:22px; font-weight:700;">{{ $group->name }}</div>
                    <div style="color:#6b7280; font-size:14px;">{{ $group->description ?? 'No description' }}</div>
                </div>
            </div>

            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div style="background:#f3f4f6; border-radius:8px; padding:10px 16px; text-align:center;">
                    <div style="font-size:20px; font-weight:700; color:#4f46e5;">{{ $group->discussions()->count() }}</div>
                    <div style="font-size:12px; color:#6b7280;">Discussions</div>
                </div>
                <div style="background:#f3f4f6; border-radius:8px; padding:10px 16px; text-align:center;">
                    <div style="font-size:20px; font-weight:700; color:#059669;">{{ $members->count() }}</div>
                    <div style="font-size:12px; color:#6b7280;">Members</div>
                </div>
                @if($creator)
                <div style="background:#f3f4f6; border-radius:8px; padding:10px 16px;">
                    <div style="font-size:12px; color:#6b7280;">Moderator</div>
                    <div style="font-size:14px; font-weight:600;">{{ $creator->first_name }} {{ $creator->last_name }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Join or View --}}
        @if(!$isMember)
        <div style="background:#fff; border-radius:12px; padding:24px; margin-bottom:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div style="font-size:16px; font-weight:600; margin-bottom:12px;">
                <i class="ti ti-file-description" style="color:#4f46e5;"></i> Group Rules & Terms
            </div>
            <p style="color:#6b7280; font-size:14px; margin-bottom:16px; line-height:1.6;">
                By joining this group, you agree to: post respectfully, avoid spamming unrelated content, 
                and respond to discussions in a timely manner. Repeated violations may result in warnings 
                or removal from the group.
            </p>
            <form method="POST" action="/groups/{{ $group->id }}/join">
                @csrf
                <button type="submit" style="background:#4f46e5; color:#fff; border:none; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:500; cursor:pointer;">
                    <i class="ti ti-check"></i> I Agree & Join Group
                </button>
            </form>
        </div>
        @else
        <div style="margin-bottom:20px;">
            <a href="/groups/{{ $group->id }}/discussions" 
               style="background:#4f46e5; color:#fff; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                <i class="ti ti-message-circle"></i> View Discussions
            </a>
        </div>
        @endif

        {{-- Members List --}}
        <div style="background:#fff; border-radius:12px; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div style="font-size:16px; font-weight:600; margin-bottom:16px;">
                <i class="ti ti-users" style="color:#4f46e5;"></i> Members ({{ $members->count() }})
            </div>
            @forelse($members as $member)
            <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                <div style="background:#ede9fe; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-weight:600; color:#4f46e5; font-size:14px;">
                    {{ strtoupper(substr($member->first_name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:14px; font-weight:500;">{{ $member->first_name }} {{ $member->last_name }}</div>
                    <div style="font-size:12px; color:#6b7280;">{{ $member->email }}</div>
                </div>
                @if($creator && $creator->id === $member->id)
                <span style="margin-left:auto; background:#ede9fe; color:#4f46e5; font-size:11px; padding:2px 8px; border-radius:20px; font-weight:500;">
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
@endsection