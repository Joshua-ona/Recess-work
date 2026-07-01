@extends('layouts.app')
@section('title', 'Notifications')

@section('body')
<div class="dash-wrap">

    @include('layouts.sidebar', [
        'role'         => 'lecturer',
        'user'         => auth()->user(),
        'myCourses'    => $myCourses   ?? collect(),
        'flaggedCount' => $flaggedCount ?? 0,
    ])

    <div class="dash-main">

        <div class="dash-header">
            <div>
                <div class="dash-header-title">Notifications</div>
                <div class="dash-header-sub">
                    @if ($warnings->isNotEmpty())
                        You have {{ $warnings->count() }} {{ Str::plural('warning', $warnings->count()) }} on file
                    @else
                        Nothing new
                    @endif
                </div>
            </div>
        </div>

        <div class="dash-body">
            <div class="full-panel">
                <div class="panel-head">
                    <span class="panel-title">Warnings from admins</span>
                </div>
                <div class="panel-body">
                    @forelse ($warnings as $warning)
                        <div class="activity-item">
                            <div class="activity-dot" style="background: var(--red-400)"></div>
                            <div>
                                <div class="activity-text">{{ $warning->message }}</div>
                                <div class="activity-time">
                                    <span class="badge badge-red">Warning</span>
                                    Sent by {{ $warning->issuer?->full_name ?? 'an admin' }}
                                    · {{ $warning->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size:13px;color:var(--muted);text-align:center;padding:1rem 0">
                            No warnings — you're in good standing.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
