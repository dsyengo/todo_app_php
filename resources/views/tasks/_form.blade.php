@php
    $isEdit = isset($task);
@endphp

<div class="space-y-5">

    {{-- Title --}}
    <div>
        <label for="title" class="label">Title <span class="text-red-600">*</span></label>
        <input id="title" type="text" name="title"
               value="{{ old('title', $task->title ?? '') }}"
               class="input @error('title') border-red-500 @enderror"
               required maxlength="255" autofocus>
        @error('title')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="label">Description</label>
        <textarea id="description" name="description" rows="3"
                  class="input @error('description') border-red-500 @enderror"
                  maxlength="2000">{{ old('description', $task->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Priority + Due date (2 columns) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="priority" class="label">Priority</label>
            <select id="priority" name="priority"
                    class="input @error('priority') border-red-500 @enderror">
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $val => $label)
                    <option value="{{ $val }}"
                        @selected(old('priority', $task->priority ?? 'medium') === $val)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('priority')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="due_at" class="label">Due date</label>
            <input id="due_at" type="datetime-local" name="due_at"
                   value="{{ old('due_at', isset($task) && $task->due_at ? $task->due_at->format('Y-m-d\TH:i') : '') }}"
                   class="input @error('due_at') border-red-500 @enderror">
            @error('due_at')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Categories --}}
    <div>
        <label class="label">Categories</label>

        @if($categories->isEmpty())
            <p class="text-xs text-brand-accent">
                No categories yet.
                <a href="{{ route('categories.index') }}" class="text-brand-primary hover:underline">Create one</a>.
            </p>
        @else
            @php
                $selected = old('categories',
                    isset($task) ? $task->categories->pluck('id')->all() : []
                );
                $selected = array_map('intval', (array) $selected);
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($categories as $category)
                    <label class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-md cursor-pointer
                                  hover:border-brand-secondary transition-colors">
                        <input type="checkbox" name="categories[]"
                               value="{{ $category->id }}"
                               @checked(in_array($category->id, $selected))
                               class="rounded border-brand-accent text-brand-primary focus:ring-brand-secondary">
                        <span class="text-sm text-brand-primary">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>

            @error('categories')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-200">
        <a href="{{ route('dashboard') }}" class="btn-ghost">Cancel</a>
        <button type="submit" class="btn-primary">
            <x-icon name="check" size="16" />
            {{ $isEdit ? 'Save changes' : 'Create task' }}
        </button>
    </div>

</div>