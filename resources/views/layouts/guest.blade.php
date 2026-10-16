<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') — Todo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 text-brand-primary font-semibold text-lg">
                <x-icon name="check-square" size="24" />
                <span>Todo</span>
            </div>
        </div>

        <div class="card p-6">
            @yield('content')
        </div>
    </div>

</body>
</html>