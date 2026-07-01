

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $discussion->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .meta { color: #666; font-size: 12px; margin-bottom: 20px; }
        .body { margin-bottom: 30px; line-height: 1.6; }
        .reply { border-left: 3px solid #007bff; padding-left: 15px; margin-bottom: 15px; }
        .reply-meta { color: #666; font-size: 11px; }
        h2 { color: #333; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>{{ $discussion->title }}</h1>
    <div class="meta">
        Posted by {{ $discussion->user->first_name ?? 'Unknown' }} 
        · {{ $discussion->created_at->format('d M Y, h:i A') }}
        · Group: {{ $group->name }}
    </div>

    <div class="body">
        {{ $discussion->body }}
    </div>

    <h2>Replies ({{ $discussion->replies->count() }})</h2>

    @forelse($discussion->replies as $reply)
    <div class="reply">
        <div class="reply-meta">
            {{ $reply->user->first_name ?? 'Unknown' }} 
            · {{ $reply->created_at->format('d M Y, h:i A') }}
        </div>
        <p>{{ $reply->body }}</p>
    </div>
    @empty
    <p>No replies yet.</p>
    @endforelse
</body>
</html>