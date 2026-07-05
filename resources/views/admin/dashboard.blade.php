@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')

<div class="flex items-center justify-between border-b pb-3 mb-6">
    <div class="flex items-center gap-3">
        <span class="font-medium text-lg">Admin Dashboard</span>
        <span class="text-sm text-gray-500">{{ auth()->user()->full_name }}</span>
    </div>
    <a href="{{ route('admin.Users.index') }}" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
        Manage all users
    </a>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-gray-100 rounded-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Total members</p>
        <p class="text-2xl font-semibold">{{ $totalMembers }}</p>
    </div>
    <div class="bg-gray-100 rounded-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Active today</p>
        <p class="text-2xl font-semibold">{{ $activeToday }}</p>
    </div>
    <div class="bg-gray-100 rounded-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Pending approvals</p>
        <p class="text-2xl font-semibold text-amber-600">{{ $pendingApprovals->count() }}</p>
    </div>
    <div class="bg-gray-100 rounded-lg p-4">
        <p class="text-sm text-gray-500 mb-1">Blacklisted</p>
        <p class="text-2xl font-semibold text-red-600">{{ $blacklistedCount }}</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Left column (spans 2) --}}
    <div class="col-span-2 space-y-6">

        {{-- Top contributors --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-semibold mb-3">Top contributors this month</p>
            <div class="space-y-2">
                @foreach ($topContributors as $c)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $c['name'] }}</span>
                        <span class="text-gray-500">{{ $c['posts'] }} posts</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-lg">
                        <div class="h-1.5 bg-green-600 rounded-lg" style="width: {{ min(100, $c['posts'] * 2.5) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Flagged content --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-medium mb-3">Flagged content</p>
            <div class="divide-y">
                @forelse ($flaggedContent as $f)
                <div class="flex justify-between items-center py-2">
                    <div>
                        <p class="text-sm">{{ $f['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $f['meta'] }}</p>
                    </div>
                    <button class="text-xs border rounded px-2 py-1">Review</button>
                </div>
                @empty
                <p class="text-sm text-gray-500 py-2">Nothing flagged right now.</p>
                @endforelse
            </div>
        </div>

        {{-- Upcoming quizzes --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-medium mb-3">Upcoming quiz configurations</p>
            <table class="w-full text-sm">
                <tr class="text-gray-500">
                    <td class="py-1">Quiz</td>
                    <td>Category</td>
                    <td class="text-right">Opens</td>
                </tr>
                @foreach ($upcomingQuizzes as $q)
                <tr class="border-t">
                    <td class="py-2">{{ $q['name'] }}</td>
                    <td>{{ $q['category'] }}</td>
                    <td class="text-right">{{ $q['opens'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>

    </div>

    {{-- Right column --}}
    <div class="space-y-6">

        {{-- Pending member approvals (self-registered lecturers) --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-medium mb-3">Pending lecturer approvals</p>
            <div class="space-y-3">
                @forelse ($pendingApprovals as $user)
                <div class="flex items-center justify-between">
                    <span class="text-sm">{{ $user->full_name }}</span>
                    <div class="flex gap-1">
                        <form method="POST" action="{{ route('admin.Users.approve', $user) }}">
                            @csrf
                            <button class="text-green-600 text-xs border rounded px-2 py-1">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.Users.decline', $user) }}">
                            @csrf
                            <button class="text-red-600 text-xs border rounded px-2 py-1">Decline</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">No pending approvals.</p>
                @endforelse
            </div>
        </div>

        {{-- Pending group approvals --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-medium mb-3">Pending group approvals</p>
            <div class="space-y-3">
                @forelse($groups as $g)
                <div class="border-b py-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-sm">{{ $g->name }}</p>
                        <p class="text-xs text-gray-500">{{ $g->description }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.groups.approve', $g) }}">
                            @csrf
                            <button class="bg-green-600 text-white px-2 py-1 rounded text-xs">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.groups.reject', $g) }}">
                            @csrf
                            <button class="bg-red-600 text-white px-2 py-1 rounded text-xs">Reject</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">No pending groups.</p>
                @endforelse
            </div>
        </div>

        {{-- Inactivity warnings --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-medium mb-3">Inactivity warnings</p>
            <div class="space-y-1">
                @forelse ($warnedMembers as $user)
                <div class="py-2 border-t first:border-t-0">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm">{{ $user->full_name }}</span>
                        @if ($user->status === 'blacklisted')
                            <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">Blacklisted</span>
                        @else
                            <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded">
                                Warning {{ $user->warning_count }} of 2
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2">
                        @if ($user->status === 'blacklisted')
                            <form method="POST" action="{{ route('admin.Users.unblacklist', $user) }}" class="flex justify-end">
                                @csrf
                                <button class="text-xs border rounded px-2 py-1">Reinstate</button>
                            </form>
                        @else
                            <details>
                                <summary class="text-xs border rounded px-2 py-1 inline-block cursor-pointer select-none">Warn</summary>
                                <form method="POST" action="{{ route('admin.Users.warn', $user) }}" class="mt-2 space-y-1">
                                    @csrf
                                    <textarea name="message" rows="2" required placeholder="Describe the rule violation…"
                                        class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                    <button class="text-xs border rounded px-2 py-1 w-full">Send warning</button>
                                </form>
                            </details>
                            <div class="flex gap-1 justify-end">
                                <form method="POST" action="{{ route('admin.Users.logout', $user) }}">
                                    @csrf
                                    <button class="text-xs border rounded px-2 py-1">Log out</button>
                                </form>
                                <form method="POST" action="{{ route('admin.Users.blacklist', $user) }}"
                                    onsubmit="return confirm('Blacklist {{ $user->full_name }} immediately?');">
                                    @csrf
                                    <button class="text-xs border rounded px-2 py-1 text-red-600">Blacklist</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 py-2">No members currently flagged for inactivity.</p>
                @endforelse
            </div>
        </div>

        {{-- ML trending topics --}}
        <div class="bg-white border rounded-lg p-5">
            <p class="font-medium mb-3">ML-recommended trending topics</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($trendingTopics as $t)
                <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">{{ $t }}</span>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
