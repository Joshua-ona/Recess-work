@extends('layouts.admin')

@section('title', 'Manage users')

@section('content')

    <div class="flex items-center justify-between border-b pb-3 mb-6">
        <div class="flex items-center gap-3">
            <span class="font-medium text-lg">Manage users</span>
            <span class="text-sm text-gray-500">{{ $users->total() }} total</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
            ← Back to dashboard
        </a>
    </div>

    <form method="GET" action="{{ route('admin.Users.index') }}" class="mb-4">
        <input
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Search by name or email…"
            class="w-full max-w-sm border rounded px-3 py-2 text-sm"
        >
    </form>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Logged in</th>
                    <th class="px-4 py-2">Warnings</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-2">{{ $user->full_name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
                        <td class="px-4 py-2">
                            @if ($user->status === 'blacklisted')
                                <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">Blacklisted</span>
                            @elseif ($user->status === 'pending')
                                <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded">Pending</span>
                            @else
                                <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded">Active</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if (in_array($user->id, $onlineIds))
                                <span class="inline-flex items-center gap-1 text-xs text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Online
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Offline</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($user->warnings->isEmpty())
                                <span class="text-xs text-gray-400">{{ $user->warning_count }} of 2</span>
                            @else
                                <details>
                                    <summary class="text-xs text-amber-700 cursor-pointer select-none">
                                        {{ $user->warning_count }} of 2 — view
                                    </summary>
                                    <div class="mt-2 space-y-2 max-w-xs">
                                        @foreach ($user->warnings as $warning)
                                            <div class="text-xs border rounded px-2 py-1.5 bg-amber-50">
                                                <p>{{ $warning->message }}</p>
                                                <p class="text-gray-400 mt-1">
                                                    {{ $warning->issuer?->full_name ?? 'Unknown admin' }} ·
                                                    {{ $warning->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if ($user->id === auth()->id())
                                <span class="text-xs text-gray-400">You</span>
                            @else
                                <div class="flex flex-col gap-2 items-end">
                                    @if ($user->status === 'blacklisted')
                                        <form method="POST" action="{{ route('admin.Users.unblacklist', $user) }}">
                                            @csrf
                                            <button class="text-xs border rounded px-2 py-1">Reinstate</button>
                                        </form>
                                    @else
                                        <details>
                                            <summary class="text-xs border rounded px-2 py-1 inline-block cursor-pointer select-none">Warn</summary>
                                            <form method="POST" action="{{ route('admin.Users.warn', $user) }}" class="mt-2 space-y-1 w-48">
                                                @csrf
                                                <textarea name="message" rows="2" required placeholder="Describe the rule violation…"
                                                          class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                                <button class="text-xs border rounded px-2 py-1 w-full">Send warning</button>
                                            </form>
                                        </details>
                                        <div class="flex gap-1">
                                            @if (in_array($user->id, $onlineIds))
                                                <form method="POST" action="{{ route('admin.Users.logout', $user) }}">
                                                    @csrf
                                                    <button class="text-xs border rounded px-2 py-1">Log out</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.Users.blacklist', $user) }}"
                                                  onsubmit="return confirm('Blacklist {{ $user->full_name }} immediately?');">
                                                @csrf
                                                <button class="text-xs border rounded px-2 py-1 text-red-600">Blacklist</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

@endsection
