@extends('layouts.app')

@section('title', 'New Discussion')

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
                <div class="dash-header-title">New Discussion</div>
                <div class="dash-header-sub">in {{ $group->name }}</div>
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

            <form method="POST" action="/groups/{{ $group->id }}/discussions">
                @csrf

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control"
                        placeholder="Discussion title" value="{{ old('title') }}" required>
                </div>

                <div class="form-group" style="margin-top:16px;">
                    <label>Body</label>
                    <textarea name="body" class="form-control"
                        rows="6" placeholder="Write your discussion here..." required>{{ old('body') }}</textarea>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ti ti-send"></i> Post Discussion
                    </button>
                    <a href="/groups/{{ $group->id }}/discussions" class="btn btn-outline btn-sm">
                        <i class="ti ti-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection