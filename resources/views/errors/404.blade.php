@extends('layouts.guest')

@section('title', 'Page not found')

@section('content')
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-bg text-brand-accent mb-4">
            <x-icon name="search" size="24" />
        </div>
        <h1 class="text-lg font-semibold text-brand-primary mb-2">Page not found</h1>
        <p class="text-sm text-brand-secondary mb-6">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <a href="{{ url('/') }}" class="btn-primary w-full justify-center">
            Back to dashboard
        </a>
    </div>
@endsection