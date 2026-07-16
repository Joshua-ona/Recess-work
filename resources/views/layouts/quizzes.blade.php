<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Quiz</title>
    
  
    @vite(['resources/css/app.css'])
    @stack('styles')
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f7fb;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quiz-full-page {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
        }
        
        /* Prevent any dashboard elements */
        .dash-wrap, .dash-main, .dash-body, .sidebar {
            display: none !important;
        }
        
        /* Full screen overlay for quiz */
        .quiz-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f5f7fb;
            z-index: 9999;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="quiz-overlay">
        <div class="quiz-full-page">
            @yield('body')
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>