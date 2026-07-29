@extends('layouts.app')
@section('title', 'Manage users')
@section('body')

<div class="dash-wrap">
    @include('layouts.sidebar', [
    'role' => 'system_admin',
    'user' => auth()->user(),
    'userCount' => $users->total() ?? 0,
    ])
    <div class="dash-main">
        <div class="dash-header">
            <div>
                <div class="dash-header-title">Manage users</div>
                <div class="dash-header-sub">{{ $users->total() }} total</div>
            </div>
        </div>

        <div class="dash-body" style="flex-direction:column;">

            @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($pendingInvites->isNotEmpty())
            <div class="full-panel" style="margin-bottom:1.5rem;">
                <div class="panel-body">
                    <div style="font-weight:600; margin-bottom:0.75rem;">
                        Pending lecturer invitations ({{ $pendingInvites->count() }})
                    </div>
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Sent</th>
                                    <th>Expires</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingInvites as $invite)
                                <tr>
                                    <td>{{ $invite->first_name }} {{ $invite->last_name }}</td>
                                    <td class="text-muted">{{ $invite->email }}</td>
                                    <td>{{ $invite->created_at->diffForHumans() }}</td>
                                    <td>
                                        @if ($invite->isExpired())
                                        <span class="badge badge-red">Expired</span>
                                        @else
                                        {{ $invite->expires_at->diffForHumans() }}
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                                            <form method="POST" action="{{ route('admin.lecturers.resend', $invite) }}">
                                                @csrf
                                                <button class="btn btn-outline btn-sm">Resend</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.lecturers.cancel', $invite) }}"
                                                onsubmit="return confirm('Cancel this invitation?');">
                                                @csrf
                                                <button class="btn btn-danger-sm">Cancel</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <form method="GET" action="{{ route('admin.users.index') }}" style="margin-bottom:1rem; max-width:360px;">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email…"
                    class="form-control">
            </form>

            <div class="full-panel">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Logged in</th>
                                <th>Warnings</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->full_name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td style="text-transform:capitalize;">{{ $user->role }}</td>
                                <td>
                                    @if ($user->status === 'blacklisted')
                                    <span class="badge badge-red">Blacklisted</span>
                                    @elseif ($user->status === 'pending')
                                    <span class="badge badge-amber">Pending</span>
                                    @else
                                    <span class="badge badge-green">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($user->id, $onlineIds))
                                    <span
                                        style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:var(--teal-600);">
                                        <span
                                            style="width:7px;height:7px;border-radius:50%;background:var(--teal-400);display:inline-block;"></span>
                                        Online
                                    </span>
                                    @else
                                    <span class="text-muted" style="font-size:12px;">Offline</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->warnings->isEmpty())
                                    <span class="text-muted" style="font-size:12px;">{{ $user->warning_count }} of
                                        2</span>
                                    @else
                                    <details>
                                        <summary style="font-size:12px; color:var(--amber-600); cursor:pointer;">
                                            {{ $user->warning_count }} of 2 — view
                                        </summary>
                                        <div
                                            style="margin-top:8px; display:flex; flex-direction:column; gap:6px; max-width:260px;">
                                            @foreach ($user->warnings as $warning)
                                            <div
                                                style="font-size:12px; border-radius:var(--radius-sm); padding:6px 8px; background:var(--amber-50);">
                                                <p style="margin:0;">{{ $warning->message }}</p>
                                                <p style="margin:4px 0 0; color:var(--muted);">
                                                    {{ $warning->issuer?->full_name ?? 'Unknown admin' }} ·
                                                    {{ $warning->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @endforeach
                                        </div>
                                    </details>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    @if ($user->id === auth()->id())
                                    <span class="text-muted" style="font-size:12px;">You</span>
                                    @else
                                    <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-end;">
                                        @if ($user->status === 'blacklisted')
                                        <form method="POST" action="{{ route('admin.users.unblacklist', $user) }}">
                                            @csrf
                                            <button class="btn btn-outline btn-sm">Reinstate</button>
                                        </form>
                                        @else
                                        <details>
                                            <summary class="btn btn-outline btn-sm"
                                                style="display:inline-flex; cursor:pointer;">Warn</summary>
                                            <form method="POST" action="{{ route('admin.users.warn', $user) }}"
                                                style="margin-top:8px; display:flex; flex-direction:column; gap:6px; width:190px;">
                                                @csrf
                                                <textarea name="message" rows="2" required
                                                    placeholder="Describe the rule violation…" class="form-control"
                                                    style="font-size:12px;"></textarea>
                                                <button class="btn btn-primary btn-sm" style="width:100%;">Send
                                                    warning</button>
                                            </form>
                                        </details>
                                        <div style="display:flex; gap:6px;">
                                            @if (in_array($user->id, $onlineIds))
                                            <form method="POST" action="{{ route('admin.users.logout', $user) }}">
                                                @csrf
                                                <button class="btn btn-outline btn-sm">Log out</button>
                                            </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.users.blacklist', $user) }}"
                                                onsubmit="return confirm('Blacklist {{ $user->full_name }} immediately?');">
                                                @csrf
                                                <button class="btn btn-danger-sm">Blacklist</button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding:2rem; color:var(--muted);">No users
                                    found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top:1rem;">
                {{ $users->links() }}
            </div>

        </div>
    </div>
</div>

@endsection