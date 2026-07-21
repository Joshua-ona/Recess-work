@extends('layouts.app')

@section('title', 'Group Statistics')

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

    {{-- Main --}}
    <div class="dash-main" style="flex:1;">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    <i class="ti ti-chart-bar"></i>
                    {{ $group->name }} Statistics
                </div>
                <div class="dash-header-sub">
                    Overview of discussions and group activity
                </div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/{{ $group->id }}/discussions" class="group-btn group-btn-outline group-btn-sm">
                    <i class="ti ti-arrow-left"></i>
                    Back to Discussions
                </a>
            </div>
        </div>

        <div class="group-dash-body">

            {{-- Statistics Cards - VERTICAL STACK --}}
            <div class="group-stat-stack">
                <div class="group-stat-card">
                    <div class="stat-icon blue">
                        <i class="ti ti-message-circle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Total Discussions</div>
                        <div class="stat-value">{{ $stats['discussions_count'] }}</div>
                    </div>
                </div>

                <div class="group-stat-card">
                    <div class="stat-icon green">
                        <i class="ti ti-messages"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Total Replies</div>
                        <div class="stat-value">{{ $stats['replies_count'] }}</div>
                    </div>
                </div>

                <div class="group-stat-card">
                    <div class="stat-icon purple">
                        <i class="ti ti-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Members</div>
                        <div class="stat-value">{{ $group->users->count() }}</div>
                    </div>
                </div>

                <div class="group-stat-card">
                    <div class="stat-icon orange">
                        <i class="ti ti-calendar"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Created</div>
                        <div class="stat-value" style="font-size:1.2rem;">{{ $group->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Latest Activity --}}
            <div class="group-card" style="padding:30px;">
                <h3 style="margin-bottom:20px;">
                    <i class="ti ti-activity"></i>
                    Latest Activity
                </h3>

                @if($stats['latest_discussion'])
                    <div style="padding:20px; background:var(--gray-50); border-radius:var(--radius-sm);">
                        <h4 style="margin-bottom:10px;">
                            {{ $stats['latest_discussion']->title }}
                        </h4>
                        <p class="text-muted">
                            {{ Str::limit($stats['latest_discussion']->body, 180) }}
                        </p>
                        <div style="margin-top:15px; color:var(--gray-500);">
                            <i class="ti ti-clock"></i>
                            {{ $stats['latest_discussion']->created_at->diffForHumans() }}
                        </div>
                    </div>
                @else
                    <div style="text-align:center; padding:50px;">
                        <i class="ti ti-chart-bar-off" style="font-size:60px; color:var(--gray-300);"></i>
                        <h3 style="margin-top:20px;">No Activity Yet</h3>
                        <p class="text-muted">This group doesn't have any discussions yet.</p>
                    </div>
                @endif
            </div>

            {{-- Summary --}}
            <div class="group-card" style="padding:25px; background:var(--gray-50);">
                <h3 style="margin-bottom:15px;">
                    <i class="ti ti-info-circle"></i>
                    Group Summary
                </h3>
                <p class="text-muted" style="line-height:1.8;">
                    This group currently has
                    <strong>{{ $stats['discussions_count'] }}</strong>
                    discussion{{ $stats['discussions_count'] != 1 ? 's' : '' }}
                    and
                    <strong>{{ $stats['replies_count'] }}</strong>
                    repl{{ $stats['replies_count'] == 1 ? 'y' : 'ies' }}
                    shared among
                    <strong>{{ $group->users->count() }}</strong>
                    member{{ $group->users->count() != 1 ? 's' : '' }}.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection