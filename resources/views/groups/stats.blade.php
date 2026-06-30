@extends('layouts.app')

@section('title', 'Group Statistics')

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