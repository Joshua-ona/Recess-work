@extends('layouts.app')

@section('title', 'Edit Group')

@section('body')
<div class="dash-wrap">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-row">
                <div class="sidebar-logo-icon"><i class="ti ti-messages"></i></div>
                <div>
                    <div class="sidebar-logo-name">EduDiscuss</div>
                    <div class="sidebar-logo-sub">E-Discussion Platform</div>
                </div>
            </div>
        </div>
        <div class="sidebar-section">
            <div class="sidebar-section-label">Main</div>
            <a href="/dashboard" class="sidebar-item"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
            <a href="/groups" class="sidebar-item active"><i class="ti ti-users-group"></i> Groups</a>
            <a href="/discussions" class="sidebar-item"><i class="ti ti-message-circle"></i> Discussions</a>
        </div>
        <div class="sidebar-spacer"></div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-meta">{{ auth()->user()->role }}</div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title">Edit Group</div>
                <div class="dash-header-sub">Update group details</div>
            </div>
        </div>

        <div class="dash-body">
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

                <div class="form-group">
                    <label>Group Name</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $group->name) }}" required>
                </div>

                <div class="form-group" style="margin-top:16px;">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        rows="4">{{ old('description', $group->description) }}</textarea>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-check"></i> Update Group
                    </button>
                    <a href="/groups" class="btn btn-outline btn-sm">
                        <i class="ti ti-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection