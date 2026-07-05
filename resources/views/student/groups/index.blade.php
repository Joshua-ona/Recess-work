@extends('layouts.app')

@section('body')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- 1. Create Group Form --}}
    <div class="bg-white p-6 rounded-lgshadow">
        <h2 class="font-bold mb-3">Request New Group</h2>
        <form method="POST" action="{{ route('student.groups.store') }}">
            @csrf
            <input name="name" placeholder="Group Name" class="borderp-2 w-full rounded mb-2" required>
            <textarea name="description" placeholder="Description" class="border p-2w-full rounded mb-2"></textarea>
            <button class="bg-blue-600text-white px-4 py-2 rounded">Request
                Approval</button>
        </form>
        @error('name') <p class="text-red-600text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    {{-- 2. My Pending Groups --}}

    <div class="bg-white p-6 rounded-lg shadow mt-4">
        <h2 class="font-bold mb-3">My Pending Groups</h2>
        @forelse($myGroups->where('status','pending') as $g)
        <div class="border-b py-2 flex justify-between">
            <span>{{ $g->name }} <span class="text-xs text-gray-500">({{ $g->pivot->role }})</span></span>
            <span class="text-xs px-2 rounded bg-green-100">{{ $g->status }}</span>
        </div>
        @empty
        <p class="text-gray-500">No Pending Requests.</p>
        @endforelse
    </div>

    <div class="bg-white p-6 rounded-lg shadow mt-4">
        <h2 class="font-bold mb-3">My Approved Groups</h2>

        @forelse($myGroups->where('status','approved') as $g)
        <a href="{{route('student.groups.show',$g)}}"
            class="block border-b py-3 hover:bg-gray-50 rounded px-2 transition">
            <div class="font-semibold text-blue-600 flex"> {{ $g->name }} </div>
            <div class="text-sm text-gray-500">{{ $g->description }} </div>
            <div class="text-xs text-gray-400 mt-1">Click to open chat </div>
        </a>
    </div>
    @empty
    <p class="text-gray-500">Your not in any approved group yet.</p>
    @endforelse
</div>


{{-- 3. Discover Approved Groups --}}
<div class="bg-white p-6 rounded-lg shadow mt-4">
    <h2 class="font-bold mb-3">Discover Groups</h2>
    @forelse($discoverGroups as $g)
    <div class="border-b py-2 flex justify-between items-center">
        <div>
            <p class="font-semibold">{{ $g->name }}</p>
            <p class="text-sm text-gray-500">{{ $g->description }}</p>
        </div>
        <form method="POST" action="{{ route('student.groups.join',$g) }}">
            @csrf
            <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">Join</button>
        </form>
    </div>
    @empty
    <p class="text-gray-500">No approved groups to join.</p>
    @endforelse
</div>
</div>
@endsection