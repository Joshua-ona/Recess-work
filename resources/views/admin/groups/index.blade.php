<x-app-layout>

    <x-app-layout>
        <x-slot name="header">Pending Groups</ x-slot>
            <div class="max-w-4xl mx-auto bg-white p-6rounded-lg shadow">
                <h2 class="font-bold mb-3">GroupsWaiting for Approval</h2>
                @forelse($groups as $g)
                <div class="border-b py-3 flexjustify-between items-center">
                    <div>
                        <p class="font-semibold">{{ $g->name }}</p>
                        <p class="text-smtext-gray-500">By: {{ $g->creator->name }} |{{ $g->description }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.groups.approve',$g) }}">
                            @csrf
                            <button class="bg-green-600text-white px-3 py-1 roundedtext-sm">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.groups.reject',$g) }}">
                            @csrf
                            <button class="bg-red-600text-white px-3 py-1 roundedtext-sm">Reject</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No pendinggroups.</p>
                @endforelse
            </div>
    </x-app-layout>