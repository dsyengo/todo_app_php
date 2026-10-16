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

<article class="group relative flex items-start gap-3 rounded-md bg-white border border-gray-200
                px-4 py-3.5 transition-all duration-150
                hover:border-brand-accent hover:shadow-sm
                {{ $task->is_completed ? 'opacity-70' : '' }}
                {{ $isHigh && ! $task->is_completed ? 'border-l-[3px] border-l-brand-primary' : '' }}">

    {{-- Toggle completion --}}
    <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="mt-0.5 shrink-0 relative z-10">
        @csrf
        @method('PATCH')
        <button type="submit"
                aria-label="{{ $task->is_completed ? 'Mark as pending' : 'Mark as completed' }}"
                class="w-5 h-5 rounded-full border transition-all duration-150 flex items-center justify-center
                       focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-brand-secondary
                       {{ $task->is_completed
                            ? 'bg-brand-secondary border-brand-secondary text-white'
                            : 'bg-white border-brand-accent hover:border-brand-primary hover:bg-brand-bg' }}">
            @if($task->is_completed)
                <x-icon name="check" size="12" />
            @else
                <span class="w-2 h-2 rounded-full bg-transparent group-hover:bg-brand-accent transition-colors"></span>
            @endif
        </button>
    </form>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-sm font-medium leading-snug break-words flex-1 min-w-0
                       {{ $task->is_completed
                            ? 'line-through text-brand-accent'
                            : 'text-brand-primary' }}">
                <a href="{{ route('tasks.show', $task) }}"
                   class="outline-none focus-visible:ring-2 focus-visible:ring-brand-secondary
                          focus-visible:ring-offset-2 rounded
                          after:absolute after:inset-0 after:z-0">
                    {{ $task->title }}
                </a>
            </h3>

            {{-- Priority pill --}}
            <span class="shrink-0 inline-flex items-center gap-1.5
                         text-[11px] font-medium uppercase tracking-wider
                         {{ $priorityText }} {{ $task->is_completed ? 'opacity-50' : '' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $priorityDot }}"></span>
                {{ $priorityLabel }}
            </span>
        </div>

        @if($task->description)
            <p class="mt-1 text-xs text-brand-secondary leading-relaxed line-clamp-2">
                {{ $task->description }}
            </p>
        @endif

        {{-- Meta row --}}
        @if($task->due_at || $task->categories->isNotEmpty())
            <div class="mt-2.5 flex items-center flex-wrap gap-x-3 gap-y-1.5 text-xs">

                {{-- Due date --}}
                @if($task->due_at)
                    <span class="inline-flex items-center gap-1.5
                                 {{ $isOverdue
                                    ? 'text-red-600 font-medium'
                                    : 'text-brand-secondary' }}">
                        <x-icon name="calendar" size="12" />
                        <span>
                            @if($isOverdue)
                                Overdue · {{ $task->due_at->diffForHumans() }}
                            @elseif(! $task->is_completed && $task->due_at->isToday())
                                Due today
                            @elseif(! $task->is_completed && $task->due_at->isTomorrow())
                                Due tomorrow
                            @else
                                {{ $task->due_at->format('M j, Y') }}
                            @endif
                        </span>
                    </span>
                @endif

                {{-- Categories --}}
                @foreach($task->categories as $category)
                    <a href="{{ route('dashboard', ['status' => 'active', 'category_id' => $category->id]) }}"
                       class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded relative z-10
                              bg-brand-bg text-brand-secondary text-[11px]
                              hover:bg-brand-accent/20 hover:text-brand-primary transition-colors">
                        <x-icon name="folder" size="11" />
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-0.5 shrink-0 relative z-10 opacity-0 group-hover:opacity-100
                transition-opacity duration-150 focus-within:opacity-100">
        <a href="{{ route('tasks.edit', $task) }}"
           aria-label="Edit task"
           title="Edit"
           class="p-1.5 rounded text-brand-accent hover:text-brand-primary hover:bg-brand-bg transition-colors">
            <x-icon name="pencil" size="15" />
        </a>

        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
              onsubmit="return confirm('Delete this task?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    aria-label="Delete task"
                    title="Delete"
                    class="p-1.5 rounded text-brand-accent hover:text-red-600 hover:bg-red-50 transition-colors">
                <x-icon name="trash-2" size="15" />
            </button>
        </form>
    </div>
</article>