@extends('layouts.app')

@section('title', 'New Discussion')

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

    {{-- Main --}}
    <div class="dash-main" style="flex:1;">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    <i class="ti ti-message-plus"></i>
                    Create Discussion
                </div>
                <div class="dash-header-sub">
                    Start a new discussion in <strong>{{ $group->name }}</strong>
                </div>
            </div>
            <a href="/groups/{{ $group->id }}/discussions" class="group-btn group-btn-outline group-btn-sm">
                <i class="ti ti-arrow-left"></i>
                Back
            </a>
        </div>

        <div class="group-dash-body">
            <div class="group-card" style="padding:35px;">

                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="/groups/{{ $group->id }}/discussions">
                    @csrf

                    {{-- Title --}}
                    <div class="form-group">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">
                            Discussion Title
                        </label>
                        <input type="text" name="title" class="form-control"
                            placeholder="Enter a clear discussion title..." value="{{ old('title') }}" required>
                    </div>

                    {{-- Body --}}
                    <div class="form-group" style="margin-top:25px;">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">
                            Discussion Content
                        </label>
                        <textarea name="body" class="form-control" rows="8"
                            placeholder="Describe your topic, ask a question or share your ideas..."
                            required>{{ old('body') }}</textarea>
                    </div>

                    {{-- Tips --}}
                    <div class="group-card"
                        style="background:#F8FAFC; margin-top:25px; padding:20px; border-left:5px solid var(--primary);">
                        <strong>
                            <i class="ti ti-bulb"></i>
                            Tips for a good discussion
                        </strong>
                        <ul style="margin-top:15px; line-height:1.9; padding-left:20px;">
                            <li>Use a short and descriptive title.</li>
                            <li>Explain your question clearly.</li>
                            <li>Stay relevant to the group topic.</li>
                            <li>Be respectful to other members.</li>
                        </ul>
                    </div>

                    {{-- Buttons --}}
                    <div style="margin-top:30px; display:flex; gap:15px;">
                        <button type="submit" class="group-btn group-btn-primary">
                            <i class="ti ti-send"></i>
                            Post Discussion
                        </button>
                        <a href="/groups/{{ $group->id }}/discussions" class="group-btn group-btn-outline">
                            <i class="ti ti-x"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection