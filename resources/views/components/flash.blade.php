@if(session('status'))
    <div x-data="{ show: true }" x-show="show" x-cloak
         class="mb-6 flex items-start gap-3 px-4 py-3 rounded-md bg-white
                border-l-4 border-brand-secondary text-brand-primary text-sm">
        <x-icon name="check-circle" size="18" class="text-brand-secondary shrink-0 mt-0.5" />
        <div class="flex-1">
            {{ session('status') }}
        </div>
        <button type="button" @click="show = false"
                aria-label="Dismiss"
                class="shrink-0 text-brand-accent hover:text-brand-primary transition-colors">
            <x-icon name="x" size="16" />
        </button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-cloak
         class="mb-6 flex items-start gap-3 px-4 py-3 rounded-md bg-white
                border-l-4 border-red-500 text-brand-primary text-sm">
        <x-icon name="alert-circle" size="18" class="text-red-500 shrink-0 mt-0.5" />
        <div class="flex-1">
            {{ session('error') }}
        </div>
        <button type="button" @click="show = false"
                aria-label="Dismiss"
                class="shrink-0 text-brand-accent hover:text-brand-primary transition-colors">
            <x-icon name="x" size="16" />
        </button>
    </div>
@endif