@extends('layouts.guest')

@section('title', 'Choose new password')

@section('content')
    <h1 class="text-lg font-semibold text-brand-primary mb-6">Choose a new password</h1>

    @if($errors->any())
        <div class="mb-4 px-3 py-2 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}"
                   class="input" required autocomplete="email">
        </div>

        <div>
            <label for="password" class="label">New password</label>
            <input id="password" type="password" name="password"
                   class="input" required autofocus autocomplete="new-password">
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="input" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            Reset password
        </button>
    </form>
@endsection