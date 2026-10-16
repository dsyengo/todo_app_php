@extends('layouts.app')

@section('title', $task->title)

@section('content')

    @php
        $isOverdue = ! $task->is_completed && $task->due_at && $task->due_at->isPast();
        $isHigh = $task->priority === 'high';

        $priorityDot = match($task->priority) {
            'high'   => 'bg-brand-primary',
            'medium' => 'bg-brand-secondary',
            'low'    => 'bg-brand-accent',
        };

        $priorityText = match($task->priority) {
            'high'   => 'text-brand-primary',
            'medium' => 'text-brand-secondary',
            'low'    => 'text-brand-accent',
        };

        $priorityLabel = match($task->priority) {
            'high'   => 'High',
            'medium' => 'Medium',
            'low'    => 'Low',
        };
    @endphp

    <div class="max-w-2xl mx-auto">

        {{-- Back --}}
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="inline-flex items-center gap-1 text-sm text-brand-secondary hover:text-brand-primary mb-4">
            <x-icon name="arrow-left" size="16" />
            Back
        </a>

        {{-- Status pill --}}
        <div class="mb-3">
            @if($task->is_completed)
                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium uppercase tracking-wider
                             text-brand-secondary">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-secondary"></span>
                    Completed
                </span>
            @elseif($isOverdue)
                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium uppercase tracking-wider
                             text-red-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Overdue
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium uppercase tracking-wider
                             text-brand-secondary">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                    Active
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-semibold text-brand-primary leading-snug mb-2
                   {{ $task->is_completed ? 'line-through text-brand-accent' : '' }}
                   {{ $isHigh && ! $task->is_completed ? 'border-l-[3px] border-l-brand-primary pl-3 -ml-3' : '' }}">
            {{ $task->title }}
        </h1>

        {{-- Description --}}
        @if($task->description)
            <div class="mt-5 text-sm text-brand-secondary leading-relaxed whitespace-pre-line">
                {{ $task->description }}
            </div>
        @else
            <p class="mt-5 text-sm text-brand-accent italic">
                No description.
            </p>
        @endif

        {{-- Details --}}
        <dl class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5
                   border-t border-gray-200 pt-6">

            {{-- Priority --}}
            <div>
                <dt class="text-[11px] uppercase tracking-wider text-brand-accent font-medium mb-1.5">
                    Priority
                </dt>
                <dd class="inline-flex items-center gap-1.5 text-sm font-medium {{ $priorityText }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $priorityDot }}"></span>
                    {{ $priorityLabel }}
                </dd>
            </div>

            {{-- Due date --}}
            <div>
                <dt class="text-[11px] uppercase tracking-wider text-brand-accent font-medium mb-1.5">
                    Due
                </dt>
                <dd class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-brand-primary' }}">
                    @if($task->due_at)
                        <span class="inline-flex items-center gap-1.5">
                            <x-icon name="calendar" size="14" />
                            {{ $task->due_at->format('M j, Y \a\t H:i') }}
                        </span>
                        <span class="block text-xs mt-0.5 {{ $isOverdue ? 'text-red-500' : 'text-brand-accent' }}">
                            {{ $task->due_at->diffForHumans() }}
                        </span>
                    @else
                        <span class="text-brand-accent">Not set</span>
                    @endif
                </dd>
            </div>

            {{-- Created --}}
            <div>
                <dt class="text-[11px] uppercase tracking-wider text-brand-accent font-medium mb-1.5">
                    Created
                </dt>
                <dd class="text-sm text-brand-primary">
                    {{ $task->created_at->format('M j, Y') }}
                    <span class="block text-xs text-brand-accent mt-0.5">
                        {{ $task->created_at->diffForHumans() }}
                    </span>
                </dd>
            </div>

            {{-- Completed --}}
            <div>
                <dt class="text-[11px] uppercase tracking-wider text-brand-accent font-medium mb-1.5">
                    Completed
                </dt>
                <dd class="text-sm text-brand-primary">
                    @if($task->completed_at)
                        {{ $task->completed_at->format('M j, Y') }}
                        <span class="block text-xs text-brand-accent mt-0.5">
                            {{ $task->completed_at->diffForHumans() }}
                        </span>
                    @else
                        <span class="text-brand-accent">Not yet</span>
                    @endif
                </dd>
            </div>

            {{-- Categories --}}
            @if($task->categories->isNotEmpty())
                <div class="sm:col-span-2">
                    <dt class="text-[11px] uppercase tracking-wider text-brand-accent font-medium mb-2">
                        Categories
                    </dt>
                    <dd class="flex flex-wrap gap-1.5">
                        @foreach($task->categories as $category)
                            <a href="{{ route('dashboard', ['status' => 'active', 'category_id' => $category->id]) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded
                                      bg-brand-bg text-brand-secondary text-xs
                                      hover:bg-brand-accent/20 hover:text-brand-primary transition-colors">
                                <x-icon name="folder" size="12" />
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </dd>
                </div>
            @endif

        </dl>

        {{-- Actions --}}
        <div class="mt-10 pt-6 border-t border-gray-200 flex items-center justify-between gap-3 flex-wrap">

            {{-- Toggle --}}
            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-ghost">
                    @if($task->is_completed)
                        <x-icon name="circle" size="16" />
                        Mark as active
                    @else
                        <x-icon name="check" size="16" />
                        Mark as completed
                    @endif
                </button>
            </form>

            <div class="flex items-center gap-2">
                {{-- Edit --}}
                <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary">
                    <x-icon name="pencil" size="16" />
                    Edit
                </a>

                {{-- Delete --}}
                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                      onsubmit="return confirm('Delete this task permanently?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <x-icon name="trash-2" size="16" />
                        Delete
                    </button>
                </form>
            </div>

        </div>

    </div>

@endsection