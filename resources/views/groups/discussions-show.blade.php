@extends('layouts.app')

@section('title', 'View Discussion')

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

    {{-- MAIN --}}
    <div class="dash-main" style="flex:1;">

        {{-- Header --}}
        <div class="dash-header">
            <div>
                <div class="dash-header-title">
                    {{ $discussion->title }}
                </div>
                <div class="dash-header-sub">
                    Posted by {{ $discussion->user->first_name ?? 'Unknown' }}
                    • {{ $discussion->created_at ? $discussion->created_at->diffForHumans() : 'Just now' }}
                </div>
            </div>
            <div class="dash-header-actions">
                <a href="/groups/{{ $group->id }}/discussions" class="group-btn group-btn-outline group-btn-sm">
                    <i class="ti ti-arrow-left"></i>
                    Back
                </a>
                <a href="https://wa.me/?text={{ urlencode($discussion->title . ' - ' . request()->url()) }}"
                    target="_blank" class="group-share-btn group-share-btn-whatsapp">
                    <i class="ti ti-brand-whatsapp"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($discussion->title) }}&url={{ urlencode(request()->url()) }}"
                    target="_blank" class="group-share-btn group-share-btn-twitter">
                    <i class="ti ti-brand-x"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
                    class="group-share-btn group-share-btn-facebook">
                    <i class="ti ti-brand-facebook"></i>
                </a>
                <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($discussion->title) }}"
                    target="_blank" class="group-share-btn group-share-btn-telegram">
                    <i class="ti ti-brand-telegram"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                    target="_blank" class="group-share-btn group-share-btn-linkedin">
                    <i class="ti ti-brand-linkedin"></i>
                </a>
            </div>
        </div>

        <div class="group-dash-body">

            {{-- Discussion --}}
            <div class="group-card">
                <div
                    style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                    <div>
                        <strong>
                            <i class="ti ti-user-circle"></i>
                            {{ $discussion->user->first_name ?? 'Unknown' }}
                        </strong>
                        <div class="text-muted" style="margin-top:4px;">
                            {{ $discussion->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="group-badge group-badge-primary">
                        {{ $discussion->replies->count() }} Replies
                    </span>
                </div>

                <hr style="margin:16px 0;">

                <div style="line-height:1.8; font-size:15px;">
                    {{ $discussion->body }}
                </div>
            </div>

            {{-- Replies Header --}}
            <div class="dash-header-title" style="margin-bottom:20px; margin-top:32px;">
                <i class="ti ti-messages"></i>
                Replies ({{ $discussion->replies->count() }})
            </div>

            {{-- Replies - VERTICAL STACK --}}
            <div class="group-reply-stack">
                @forelse($discussion->replies as $reply)
                <div class="group-reply-item">
                    <div class="reply-header">
                        <div class="reply-author">
                            <div class="avatar-sm">
                                {{ substr($reply->user->first_name ?? 'U', 0, 1) }}
                            </div>
                            {{ $reply->user->first_name ?? 'Unknown' }}
                        </div>
                        <div class="reply-time">
                            <i class="ti ti-clock"></i>
                            {{ $reply->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="reply-body">
                        {{ $reply->body }}
                    </div>
                </div>
                @empty
                <div class="group-reply-item"
                    style="text-align:center; padding:40px; border-left-color: var(--gray-300);">
                    <i class="ti ti-message-circle" style="font-size:36px; color:var(--gray-300);"></i>
                    <h4 style="margin-top:12px;">No replies yet</h4>
                    <p class="text-muted">Start the discussion by posting the first reply.</p>
                </div>
                @endforelse
            </div>

            {{-- Reply Form --}}
            <div class="group-card" style="margin-top:35px;">
                <h3 style="margin-bottom:20px;">
                    <i class="ti ti-edit"></i>
                    Write a Reply
                </h3>

                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="/groups/{{ $group->id }}/discussions/{{ $discussion->id }}/replies">
                    @csrf

                    <div class="form-group">
                        <textarea name="body" class="form-control" rows="5" placeholder="Share your thoughts..."
                            required>{{ old('body') }}</textarea>
                    </div>

                    <div style="margin-top:20px;">
                        <button type="submit" class="group-btn group-btn-primary">
                            <i class="ti ti-send"></i>
                            Post Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection