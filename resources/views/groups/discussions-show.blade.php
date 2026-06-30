@extends('layouts.app')

@section('title', 'View Discussion')


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
                <div class="dash-header-title">{{ $discussion->title }}</div>
                <div class="dash-header-sub">
                    Posted by {{ $discussion->user->first_name ?? 'Unknown' }}
                    · {{ $discussion->created_at->diffForHumans() }}
                </div>
            </div>
           <div class="dash-header-actions">
           {{-- Share buttons --}}
<a href="https://wa.me/?text={{ urlencode($discussion->title . ' - ' . request()->url()) }}" 
   target="_blank" class="btn btn-sm" style="background:#25D366; color:#fff;">
    <i class="ti ti-brand-whatsapp"></i> WhatsApp
</a>
<a href="https://twitter.com/intent/tweet?text={{ urlencode($discussion->title) }}&url={{ urlencode(request()->url()) }}" 
   target="_blank" class="btn btn-sm" style="background:#000; color:#fff;">
    <i class="ti ti-brand-x"></i> Twitter
</a>
<a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
   target="_blank" class="btn btn-sm" style="background:#1877F2; color:#fff;">
    <i class="ti ti-brand-facebook"></i> Facebook
</a>
<a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($discussion->title) }}" 
   target="_blank" class="btn btn-sm" style="background:#26A5E4; color:#fff;">
    <i class="ti ti-brand-telegram"></i> Telegram
</a>
<a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
   target="_blank" class="btn btn-sm" style="background:#0A66C2; color:#fff;">
    <i class="ti ti-brand-linkedin"></i> LinkedIn
</a>
</div>
        </div>

        <div class="dash-body">

            {{-- ORIGINAL POST --}}
            <div class="stat-card" style="padding:24px; margin-bottom:20px;">
                <p>{{ $discussion->body }}</p>
            </div>

            {{-- REPLIES --}}
            <div class="dash-header-title" style="margin-bottom:12px;">
                {{ $discussion->replies->count() }} Replies
            </div>

            @forelse($discussion->replies as $reply)
            <div class="stat-card" style="padding:16px; margin-bottom:12px;">
                <div class="stat-label">
                    <i class="ti ti-user"></i>
                    {{ $reply->user->first_name ?? 'Unknown' }}
                    · {{ $reply->created_at->diffForHumans() }}
                </div>
                <p style="margin-top:8px;">{{ $reply->body }}</p>
            </div>
            @empty
            <p class="text-muted">No replies yet. Be the first to reply!</p>
            @endforelse

            {{-- REPLY FORM --}}
            <div style="margin-top:24px;">
                <div class="dash-header-title" style="margin-bottom:12px;">Post a Reply</div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" 
                    action="/groups/{{ $group->id }}/discussions/{{ $discussion->id }}/replies">
                    @csrf
                    <div class="form-group">
                        <textarea name="body" class="form-control"
                            rows="4" placeholder="Write your reply..." required>{{ old('body') }}</textarea>
                    </div>
                    <div style="margin-top:12px;">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-send"></i> Post Reply
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection


            