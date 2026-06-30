<div class="dash-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $isMember = $group->members()->where('user_id', auth()->id())->exists();
    @endphp

    @if(!$isMember)
    <div class="stat-card" style="padding:24px; margin-bottom:20px;">
        <h3>Group Rules & Terms</h3>
        <p class="text-muted" style="margin:12px 0;">
            By joining this group, you agree to: post respectfully, avoid spamming unrelated content, 
            and respond to discussions in a timely manner. Repeated violations may result in warnings 
            or removal from the group.
        </p>
        <form method="POST" action="/groups/{{ $group->id }}/join">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="ti ti-check"></i> I Agree & Join Group
            </button>
        </form>
    </div>
    @else
    <a href="/groups/{{ $group->id }}/discussions" class="btn btn-primary btn-sm">
        <i class="ti ti-message-circle"></i> View Discussions
    </a>
    @endif
</div>