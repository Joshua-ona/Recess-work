@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">

    <div class="mb-4">
        <h2 class="font-bold text-xl">Pending Groups</h2>
    </div>

    <h3 class="font-bold mb-3">Groups Waiting for Approval</h3>

    @forelse($groups as $g)

        <div class="border-b py-3 flex justify-between items-center">

            <div>
                <p class="font-semibold">{{ $g->name }}</p>

                <p class="text-sm text-gray-500">
                    By: {{ $g->creator->name }} |
                    {{ $g->description }}
                </p>
            </div>

            <div class="flex gap-2">

                <form method="POST" action="{{ route('admin.groups.approve', $g) }}">
                    @csrf
                    <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                        Approve
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.groups.reject', $g) }}">
                    @csrf
                    <button class="bg-red-600 text-white px-3 py-1 rounded text-sm">
                        Reject
                    </button>
                </form>

            </div>

        </div>

    @empty

        <p class="text-gray-500">
            No pending groups.
        </p>

    @endforelse

</div>

@endsection