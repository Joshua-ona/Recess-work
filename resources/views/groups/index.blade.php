@extends('layouts.app')

@section('title', 'Groups')

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
    <div class="dash-main" style="flex:1; width:100%;">

        {{-- HEADER --}}
        <div class="dash-header" style="width:100%;">
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

        {{-- SEARCH BAR --}}
        <div style="padding:16px 24px 0 24px;">
            <input type="text" id="groupSearch" placeholder="🔍 Search groups..." 
                onkeyup="filterGroups()"
                style="width:100%; padding:10px 16px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; outline:none; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        </div>

        {{-- BODY --}}
        <div class="dash-body" style="padding:24px; max-width:100%;">

            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
            @endif

            {{-- MY GROUPS --}}
            <div style="margin-bottom:32px;">
                <div style="font-size:16px; font-weight:600; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                    <i class="ti ti-users-group" style="color:#4f46e5;"></i> My Groups
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
                    @forelse($myGroups as $group)
                    <div class="group-card" data-name="{{ $group->name }}"
                         style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; display:flex; flex-direction:column; gap:10px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="background:#ede9fe; border-radius:10px; width:42px; height:42px; display:flex; align-items:center; justify-content:center;">
                                <i class="ti ti-users-group" style="color:#4f46e5; font-size:20px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:15px;">{{ $group->name }}</div>
                                <div style="font-size:12px; color:#6b7280;">{{ $group->discussions()->count() }} discussions</div>
                            </div>
                        </div>
                        <div style="font-size:13px; color:#6b7280; flex:1;">
                            {{ $group->description ?? 'No description' }}
                        </div>
                        <a href="/groups/{{ $group->id }}" 
                           style="background:#4f46e5; color:#fff; border-radius:8px; padding:8px 16px; text-align:center; font-size:13px; font-weight:500; text-decoration:none; display:block;">
                            <i class="ti ti-arrow-right"></i> Open Group
                        </a>
                    </div>
                    @empty
                    <div style="background:#f9fafb; border:1px dashed #d1d5db; border-radius:12px; padding:32px; text-align:center; color:#9ca3af;">
                        <i class="ti ti-users-group" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                        You haven't joined any groups yet.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- AVAILABLE GROUPS --}}
            <div>
                <div style="font-size:16px; font-weight:600; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                    <i class="ti ti-world" style="color:#059669;"></i> Available Groups
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
                    @forelse($availableGroups as $group)
                    <div class="group-card" data-name="{{ $group->name }}"
                         style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; display:flex; flex-direction:column; gap:10px; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="background:#d1fae5; border-radius:10px; width:42px; height:42px; display:flex; align-items:center; justify-content:center;">
                                <i class="ti ti-users-group" style="color:#059669; font-size:20px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:15px;">{{ $group->name }}</div>
                                <div style="font-size:12px; color:#6b7280;">{{ $group->discussions()->count() }} discussions</div>
                            </div>
                        </div>
                        <div style="font-size:13px; color:#6b7280; flex:1;">
                            {{ $group->description ?? 'No description' }}
                        </div>
                        <a href="/groups/{{ $group->id }}" 
                           style="background:#059669; color:#fff; border-radius:8px; padding:8px 16px; text-align:center; font-size:13px; font-weight:500; text-decoration:none; display:block;">
                            <i class="ti ti-door-enter"></i> View & Join
                        </a>
                    </div>
                    @empty
                    <div style="background:#f9fafb; border:1px dashed #d1d5db; border-radius:12px; padding:32px; text-align:center; color:#9ca3af;">
                        <i class="ti ti-world" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                        No new groups available.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function filterGroups() {
    const input = document.getElementById('groupSearch').value.toLowerCase();
    const cards = document.querySelectorAll('.group-card');
    cards.forEach(card => {
        const name = card.getAttribute('data-name').toLowerCase();
        card.style.display = name.includes(input) ? '' : 'none';
    });
}
</script>

@endsection