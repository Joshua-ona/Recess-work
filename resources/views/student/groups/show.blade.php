@extends('student.dashboard')

@section('dash-body')
<div class="max-w-7xl mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">{{ $group->name }}</h1>
    <div class="grid grid-cols-12 gap-4 h-[70vh]">
        {{-- Left: Admin + Members --}}
        <div class="col-span-12 md:col-span-3 bg-white rounded-lg border flex-col">
            <div class="p-3 border-b font-semibold text-sm">Members
                ({{ $members->count() + 1 }})</div>
            <div class="p-3 space-y-2 overflow-y-auto">
                {{-- Admin on top --}}
                <div class="flex items-center gap-2 text-sm font-semibold pb-2 border-b">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">A
                    </div>
                    <div>
                        {{ $admin->name }}
                        <div class="text-xs text-blue-600 font-normal">Admin</div>
                    </div>
                </div>
                {{-- Members below --}}
                @foreach($members as $m)
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                    {{ $m->name }}
                </div>
                @endforeach
            </div>
        </div>
        {{-- Right: Chat --}}
        <div class="col-span-12 md:col-span-9 bg-white rounded-lg border flex-col">
            <div class="p-3 border-b font-semibold text-sm">
                Group Chat
            </div>
            {{-- Messages area --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="chat-box">
                @forelse($messages as $c)
                <div class="text-sm">
                    <span class="font-semibold text-blue-700">{{ $c->user->name }}:</span>
                    <span class="text-gray-800">{{ $c->body }}</span>
                    <div class="text-[10px] text-gray-400">{{ $c->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <p class="text-gray-500 text-center mt-10">No messages yet. Say hi
                    👋
                </p>
                @endforelse
            </div>
            {{-- Send form --}}
            <form method="POST" action="{{ route('student.groups.message', $group) }}" class="p-3 border-t">
                @csrf
                <div class="flex gap-2">
                    <input name="body" placeholder="Type a message..." class="flex-1 border
rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required maxlength="1000">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 rounded-lg text-sm font-semibold">
                        Send
                    </button>
                </div>
                @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </form>
        </div>
    </div>
</div>