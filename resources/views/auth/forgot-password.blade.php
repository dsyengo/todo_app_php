@extends('layouts.guest')

@section('title', 'Reset password')

@section('content')
    <h1 class="text-lg font-semibold text-brand-primary mb-2">Reset password</h1>
    <p class="text-sm text-brand-secondary mb-6">
        Enter your email and we'll send you a link to choose a new password.
    </p>

    @if(session('status'))
        <div class="mb-4 px-3 py-2 rounded-md bg-white border border-gray-200 text-brand-primary text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-3 py-2 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="input" required autofocus autocomplete="email">
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            Send reset link
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-gray-200 text-center text-sm">
        <a href="{{ route('login') }}" class="text-brand-primary font-medium hover:underline">Back to sign in</a>
    </div>
@endsection