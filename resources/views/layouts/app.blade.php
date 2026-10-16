<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tasks') — Todo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

    <header class="border-b border-gray-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-brand-primary font-semibold">
                <x-icon name="check-square" size="22" />
                <span>Todo</span>
            </a>

            @auth
                <nav class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('categories.index') }}"
                       class="{{ request()->routeIs('categories.*') ? 'nav-link-active' : 'nav-link' }}">
                        Categories
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="nav-link inline-flex items-center gap-1">
                            <x-icon name="log-out" size="16" />
                            Logout
                        </button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main class="flex-1 max-w-5xl w-full mx-auto px-4 py-8">
        @if(session('status'))
            <div class="mb-6 px-4 py-3 rounded-md bg-white border-l-4 border-brand-secondary text-brand-primary text-sm">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 py-4 text-xs text-brand-accent">
            Todo App
        </div>
    </footer>

</body>
</html>