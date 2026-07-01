@extends('layouts.app')

@section('title', 'Edit Group')

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