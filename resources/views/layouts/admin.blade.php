<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin dashboard')</title>
    @vite('resources/css/tailwind.css')
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="max-w-6xl mx-auto px-6 py-6">
        <div class="flex items-center justify-end gap-3 mb-4 text-sm text-gray-500">
            @auth
            <span>{{ auth()->user()->full_name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="border rounded px-3 py-1 text-xs hover:bg-gray-50">
                    Log out
                </button>
            </form>
            @endauth
        </div>

        @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 text-green-800 text-sm px-4 py-2">
            {{ session('status') }}
        </div>
        @endif

        @yield('content')
    </div>
</body>

</html>