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
                    Messages
                </div>

                <div class="dash-header-sub">
                    Chat with students and lecturers
                </div>
            </div>
        </header>

        <div class="dash-body">

            <livewire:chat />

        </div>

    </main>

</div>

@endsection