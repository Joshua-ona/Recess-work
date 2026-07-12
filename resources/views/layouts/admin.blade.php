<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.4.0/tabler-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-6xl mx-auto px-6 py-6">

        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 text-sm text-gray-500">
            <div class="flex items-center gap-3">
                <i class="ti ti-messages text-lg text-gray-600"></i>
                <span class="font-semibold text-gray-800">EduDiscuss Admin</span>
            </div>
            @auth
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.Users.index') }}" class="hover:text-gray-800">Users</a>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-800">Dashboard</a>
                <span class="text-gray-400">|</span>
                <span>{{ auth()->user()->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="border rounded px-3 py-1 text-xs hover:bg-gray-100 text-gray-700">
                        Log out
                    </button>
                </form>
            </div>
            @endauth
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 text-green-800 text-sm px-4 py-2">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 text-red-700 text-sm px-4 py-2">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
