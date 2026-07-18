@extends('layouts.app')

@section('title', 'Messages')

@section('body')

<div class="dash-wrap">
    @include('layouts.sidebar', [
        'role' => auth()->user()->role,
        'user' => auth()->user(),
        'enrolledCourses' => collect(),
        'unreadCount' => 0,
        'notifCount' => 0,
    ])

    <main class="dash-main">
        <header class="dash-header">
            <div>
                <div class="dash-header-title">
                    <i class="ti ti-messages" style="margin-right: 8px;"></i>
                    Messages
                </div>
                <div class="dash-header-sub">
                    Chat with students and lecturers
                </div>
            </div>
            <div class="dash-header-actions">
                <button class="icon-btn" onclick="window.location.reload()" title="Refresh">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        </header>

        {{-- FIX: Added proper container with overflow hidden --}}
        <div class="dash-body" style="padding: 1.5rem; flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <livewire:chat />
        </div>
    </main>
</div>

@endsection