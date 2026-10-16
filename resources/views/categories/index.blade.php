@extends('layouts.app')

@section('title', 'Categories')

@section('content')

    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-brand-primary">Categories</h1>
            <p class="text-xs text-brand-accent mt-0.5">
                Group tasks into buckets like Work, Personal, or Shopping.
            </p>
        </div>

        {{-- Create form --}}
        <div class="card p-4 mb-6">
            <form method="POST" action="{{ route('categories.store') }}" class="flex items-start gap-2">
                @csrf
                <div class="flex-1">
                    <label for="name" class="label">New category</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. Work"
                           class="input @error('name') border-red-500 @enderror"
                           required maxlength="50" autofocus>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-primary mt-6">
                    <x-icon name="plus" size="16" />
                    Add
                </button>
            </form>
        </div>

        {{-- List --}}
        @if($categories->isEmpty())
            <div class="card p-12 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-brand-bg text-brand-accent mb-3">
                    <x-icon name="folder" size="24" />
                </div>
                <p class="text-sm text-brand-secondary">
                    No categories yet. Create one above to start grouping tasks.
                </p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($categories as $category)
                    <div class="card p-3"
                         x-data="{ editing: false }">

                        {{-- Display mode --}}
                        <div x-show="! editing" class="flex items-center gap-3">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <x-icon name="folder" size="16" class="text-brand-accent shrink-0" />
                                <span class="text-sm font-medium text-brand-primary truncate">
                                    {{ $category->name }}
                                </span>
                                <span class="text-xs text-brand-accent shrink-0">
                                    {{ $category->tasks_count }}
                                    {{ Str::plural('task', $category->tasks_count) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button"
                                        @click="editing = true; $nextTick(() => $refs.renameInput.focus())"
                                        aria-label="Rename category"
                                        class="p-1.5 rounded text-brand-accent hover:text-brand-primary hover:bg-brand-bg transition-colors">
                                    <x-icon name="pencil" size="16" />
                                </button>

                                <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete this category? Tasks in it will not be deleted.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            aria-label="Delete category"
                                            class="p-1.5 rounded text-brand-accent hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <x-icon name="trash-2" size="16" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit mode --}}
                        <form x-show="editing" x-cloak
                              method="POST" action="{{ route('categories.update', $category) }}"
                              class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $category->name }}"
                                   x-ref="renameInput"
                                   class="input flex-1"
                                   required maxlength="50">
                            <button type="submit" class="btn-primary">
                                <x-icon name="check" size="16" />
                                Save
                            </button>
                            <button type="button" @click="editing = false" class="btn-ghost">
                                Cancel
                            </button>
                        </form>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

@endsection