@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
    <h1 class="text-lg font-semibold text-brand-primary mb-6">Sign in</h1>

    @if($errors->any())
        <div class="mb-4 px-3 py-2 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input" required autofocus autocomplete="email">
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password"
                   class="input" required autocomplete="current-password">
        </div>

        <label class="flex items-center gap-2 text-sm text-brand-secondary">
            <input type="checkbox" name="remember" value="1"
                   class="rounded border-brand-accent text-brand-primary focus:ring-brand-secondary">
            Remember me
        </label>

        <button type="submit" class="btn-primary w-full justify-center">
            Sign in
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-gray-200 text-center text-sm text-brand-secondary">
        <a href="{{ route('password.request') }}" class="hover:text-brand-primary">Forgot your password?</a>
    </div>

    <div class="mt-3 text-center text-sm text-brand-secondary">
        No account?
        <a href="{{ route('register') }}" class="text-brand-primary font-medium hover:underline">Create one</a>
    </div>
@endsection