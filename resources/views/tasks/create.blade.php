@extends('layouts.app')

@section('title', 'New task')

@section('content')

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-sm text-brand-secondary hover:text-brand-primary">
                <x-icon name="arrow-left" size="16" />
                Back to tasks
            </a>
            <h1 class="text-xl font-semibold text-brand-primary mt-3">New task</h1>
        </div>

        <div class="card p-6">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf
                @include('tasks._form')
            </form>
        </div>

    </div>

@endsection