@extends('layouts.app')
@section('title', 'Notifications')

@section('body')
<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ])

    <div class="dash-main">

        <div class="dash-header">
            <div>
                <div class="dash-header-title">Notifications</div>
                <div class="dash-header-sub">
                    @if ($notifications->isNotEmpty())
                        You have {{ $notifications->count() }} {{ Str::plural('warning', $notifications->count()) }} on file
                    @else
                        Nothing new
                    @endif
                </div>
            </div>
        </div>

        <div class="dash-body">
            <div class="full-panel">
                <div class="panel-head">
                    <span class="panel-title">Get notified 😁</span>
                </div>
                <div class="panel-body">
 @forelse($notifications as $notification)

<div class="activity-item notification-card">

    <div class="activity-dot"
        style="background:
        {{ $notification['type'] == 'warning'
            ? 'var(--red-400)'
            : ($notification['type'] == 'message'
                ? 'var(--green-400)'
                : 'var(--blue-400)') }}">
    </div>

    <div>

      <div class="activity-text">
    @if($notification['type'] == 'message')
        <strong> New message from {{ $notification['sender'] }}</strong>

    @elseif($notification['type'] == 'quiz')
        {{ $notification['message'] }}

    @else
        {{ $notification['message'] }}
    @endif
</div>

        <div class="activity-time">

            @if($notification['type'] == 'warning')
                <span class="badge badge-red">Warning</span>
            @elseif($notification['type'] == 'message')
                <span class="badge badge-green">Message</span>
            @else
                <span class="badge badge-blue">Quiz</span>
            @endif

    
            · {{ $notification['created_at']->diffForHumans() }}

        </div>

    </div>

</div>

@empty

<p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">
    No notifications.
</p>

@endforelse
