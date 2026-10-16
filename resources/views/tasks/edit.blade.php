@extends('layouts.app')

@section('title', 'Edit task')

@section('content')

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-sm text-brand-secondary hover:text-brand-primary">
                <x-icon name="arrow-left" size="16" />
                Back to tasks
            </a>
            <h1 class="text-xl font-semibold text-brand-primary mt-3">Edit task</h1>
        </div>

        <div class="card p-6">
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')
                @include('tasks._form')
            </form>
        </div>

        {{-- Delete --}}
        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                  onsubmit="return confirm('Delete this task permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <x-icon name="trash-2" size="16" />
                    Delete task
                </button>
            </form>
        </div>

    </div>

@endsection