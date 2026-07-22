@extends('layouts.app')

@section('title', 'Edit Group')

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
                    <i class="ti ti-edit"></i>
                    Edit Group
                </div>
                <div class="dash-header-sub">
                    Update the details of <strong>{{ $group->name }}</strong>
                </div>
            </div>
            <a href="/groups" class="group-btn group-btn-outline group-btn-sm">
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

                <form method="POST" action="/groups/{{ $group->id }}">
                    @csrf
                    @method('PUT')

                    {{-- Group Name --}}
                    <div class="form-group">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">
                            Group Name
                        </label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $group->name) }}"
                            placeholder="Enter the group name..."
                            required>
                    </div>

                    {{-- Description --}}
                    <div class="form-group" style="margin-top:25px;">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">
                            Description
                        </label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="6"
                            placeholder="Describe the purpose of this group...">{{ old('description', $group->description) }}</textarea>
                    </div>

                    {{-- Information Box --}}
                    <div class="group-card" style="background:#F8FAFC; margin-top:25px; padding:20px; border-left:5px solid var(--warning);">
                        <strong>
                            <i class="ti ti-info-circle"></i>
                            Editing Tips
                        </strong>
                        <ul style="margin-top:15px; line-height:1.8; padding-left:20px;">
                            <li>Choose a clear and descriptive group name.</li>
                            <li>Keep the description relevant to the group's purpose.</li>
                            <li>Changes will be visible to all current members.</li>
                            <li>Avoid changing the group's focus too frequently.</li>
                        </ul>
                    </div>

                    {{-- Buttons --}}
                    <div style="margin-top:30px; display:flex; gap:15px;">
                        <button type="submit" class="group-btn group-btn-primary">
                            <i class="ti ti-device-floppy"></i>
                            Save Changes
                        </button>
                        <a href="/groups" class="group-btn group-btn-outline">
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