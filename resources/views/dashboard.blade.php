@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-brand-primary tracking-tight">Dashboard</h1>
            <p class="text-sm text-brand-secondary mt-1">
                Welcome back, {{ auth()->user()->name }}.
            </p>
        </div>

        <a href="{{ route('tasks.create') }}" class="btn-primary">
            <x-icon name="plus" size="16" />
            New task
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">

        {{-- Total / Completion --}}
        <div class="card p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-brand-accent font-medium">
                    Progress
                </span>
                <x-icon name="trending-up" size="16" class="text-brand-secondary" />
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold text-brand-primary">
                    {{ $stats['completion_rate'] }}%
                </span>
                <span class="text-xs text-brand-accent">
                    {{ $stats['completed'] }}/{{ $stats['total'] }}
                </span>
            </div>
            <div class="mt-3 h-1.5 rounded-full bg-brand-bg overflow-hidden">
                <div class="h-full bg-brand-primary rounded-full transition-all duration-300"
                     style="width: {{ $stats['completion_rate'] }}%"></div>
            </div>
        </div>

        {{-- Pending --}}
        <a href="{{ route('dashboard', ['status' => 'active']) }}"
           class="card p-4 hover:border-brand-accent transition-colors group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-brand-accent font-medium">
                    Active
                </span>
                <x-icon name="circle-dot" size="16" class="text-brand-secondary" />
            </div>
            <span class="text-2xl font-semibold text-brand-primary block">
                {{ $stats['pending'] }}
            </span>
            <span class="text-xs text-brand-accent mt-1 block">
                @if($stats['due_today'] > 0)
                    {{ $stats['due_today'] }} due today
                @else
                    Nothing due today
                @endif
            </span>
        </a>

        {{-- Completed --}}
        <a href="{{ route('dashboard', ['status' => 'completed']) }}"
           class="card p-4 hover:border-brand-accent transition-colors group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider text-brand-accent font-medium">
                    Completed
                </span>
                <x-icon name="circle-check" size="16" class="text-brand-secondary" />
            </div>
            <span class="text-2xl font-semibold text-brand-primary block">
                {{ $stats['completed'] }}
            </span>
            <span class="text-xs text-brand-accent mt-1 block">
                {{ $stats['due_this_week'] }} due this week
            </span>
        </a>

        {{-- Overdue --}}
        <a href="{{ route('dashboard', ['status' => 'overdue']) }}"
           class="card p-4 hover:border-brand-accent transition-colors group
                  {{ $stats['overdue'] > 0 ? 'border-red-200' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-wider font-medium
                             {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-brand-accent' }}">
                    Overdue
                </span>
                <x-icon name="alert-triangle" size="16"
                        class="{{ $stats['overdue'] > 0 ? 'text-red-500' : 'text-brand-secondary' }}" />
            </div>
            <span class="text-2xl font-semibold block
                         {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-brand-primary' }}">
                {{ $stats['overdue'] }}
            </span>
            <span class="text-xs mt-1 block
                         {{ $stats['overdue'] > 0 ? 'text-red-500' : 'text-brand-accent' }}">
                @if($stats['overdue'] === 0)
                    All caught up
                @elseif($stats['overdue'] === 1)
                    1 task needs attention
                @else
                    {{ $stats['overdue'] }} tasks need attention
                @endif
            </span>
        </a>

    </div>

    {{-- Filter tabs --}}
    <div class="mb-4 flex items-center gap-1 border-b border-gray-200">
        @php
            $tabs = [
                'active'    => ['label' => 'Active',    'count' => $stats['pending']],
                'completed' => ['label' => 'Completed', 'count' => $stats['completed']],
                'overdue'   => ['label' => 'Overdue',   'count' => $stats['overdue']],
                'all'       => ['label' => 'All',       'count' => $stats['total']],
            ];
            $query = request()->except(['status', 'page']);
        @endphp

        @foreach($tabs as $key => $tab)
            <a href="{{ route('dashboard', array_merge($query, ['status' => $key])) }}"
               class="px-3 py-2 text-sm border-b-2 -mb-px transition-colors inline-flex items-center gap-2
                   {{ $status === $key
                        ? 'border-brand-primary text-brand-primary font-medium'
                        : 'border-transparent text-brand-secondary hover:text-brand-primary' }}">
                {{ $tab['label'] }}
                <span class="text-[11px] px-1.5 py-0.5 rounded
                             {{ $status === $key
                                ? 'bg-brand-bg text-brand-secondary'
                                : 'bg-brand-bg text-brand-accent' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Search + secondary filters --}}
    <form method="GET" action="{{ route('dashboard') }}" class="mb-6">
        <input type="hidden" name="status" value="{{ $status }}">

        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative flex-1 min-w-64">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-brand-accent pointer-events-none">
                    <x-icon name="search" size="16" />
                </span>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Search tasks..."
                       class="input pl-9">
            </div>

            <select name="priority" class="input w-auto" onchange="this.form.submit()">
                <option value="">Any priority</option>
                @foreach(['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $val => $label)
                    <option value="{{ $val }}" @selected(($filters['priority'] ?? '') === $val)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="category_id" class="input w-auto" onchange="this.form.submit()">
                <option value="">Any category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-ghost">Apply</button>

            @if($hasFilters || $status !== 'active')
                <a href="{{ route('dashboard') }}" class="btn-ghost">
                    <x-icon name="rotate-ccw" size="14" />
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Task list --}}
    <div class="space-y-2">
        @forelse($tasks as $task)
            @include('tasks._row', ['task' => $task])
        @empty
            <div class="card p-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-brand-bg text-brand-accent mb-3">
                    <x-icon name="check-circle" size="24" />
                </div>
                <p class="text-sm text-brand-secondary">
                    @if($status === 'completed')
                        No completed tasks yet.
                    @elseif($status === 'overdue')
                        Nothing overdue. Nice.
                    @elseif($hasFilters)
                        No tasks match these filters.
                    @else
                        No tasks yet. Create your first one.
                    @endif
                </p>

                @if($status === 'active' && ! $hasFilters)
                    <a href="{{ route('tasks.create') }}" class="btn-primary mt-4">
                        <x-icon name="plus" size="16" />
                        New task
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tasks->hasPages())
        <div class="mt-6">
            {{ $tasks->links('pagination::tailwind') }}
        </div>
    @endif

@endsection