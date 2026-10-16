@extends('layouts.guest')

@section('title', 'Create account')

@section('content')
    <h1 class="text-lg font-semibold text-brand-primary mb-6">Create account</h1>

    @if($errors->any())
        <div class="mb-4 px-3 py-2 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="input" required autofocus autocomplete="name">
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input" required autocomplete="email">
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password"
                   class="input" required autocomplete="new-password">
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="input" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            Create account
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-gray-200 text-center text-sm text-brand-secondary">
        Already have an account?
        <a href="{{ route('login') }}" class="text-brand-primary font-medium hover:underline">Sign in</a>
    </div>
@endsection