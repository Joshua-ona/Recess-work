@extends('layouts.admin')

@section('title', 'Add a lecturer')

@section('content')

    <div class="flex items-center justify-between border-b pb-3 mb-6">
        <span class="font-medium text-lg">Add a lecturer</span>
        <a href="{{ route('admin.Users.index') }}" class="text-sm border rounded px-3 py-1.5 hover:bg-gray-50">
            ← Back to manage users
        </a>
    </div>

    <div class="bg-white border rounded-lg p-6 max-w-md">
        <p class="text-sm text-gray-500 mb-4">
            This creates the account and emails the lecturer an activation
            link. They'll set their own password and won't be able to sign
            in until they do.
        </p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 text-red-700 text-sm px-4 py-2">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.lecturers.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm mb-1" for="first_name">First name</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                           class="w-full border rounded px-3 py-2 text-sm" required autofocus>
                </div>
                <div>
                    <label class="block text-sm mb-1" for="last_name">Last name</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                           class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
            </div>

            <div>
                <label class="block text-sm mb-1" for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2 text-sm" placeholder="lecturer@university.ac.ug" required>
            </div>

            <button type="submit" class="text-sm bg-gray-900 text-white rounded px-4 py-2 hover:bg-gray-800">
                Send invitation
            </button>
        </form>
    </div>

@endsection
