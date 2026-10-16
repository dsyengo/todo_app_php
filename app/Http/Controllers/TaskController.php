<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function create()
    {
        return view('tasks.create', [
            'categories' => auth()->user()->categories()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateTask($request);

        $categoryIds = $this->validatedCategoryIds($request, $data['categories'] ?? []);

        unset($data['categories']);

        $task = $request->user()->tasks()->create($data);

        if ($categoryIds) {
            $task->categories()->sync($categoryIds);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Task created.');
    }

    public function show(Task $task)
    {
        $this->authorizeOwnership($task);

        return view('tasks.show', [
            'task' => $task->load('categories:id,name'),
        ]);
    }

    public function edit(Task $task)
    {
        $this->authorizeOwnership($task);

        return view('tasks.edit', [
            'task' => $task->load('categories:id,name'),
            'categories' => auth()->user()->categories()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeOwnership($task);

        $data = $this->validateTask($request, $task);

        $categoryIds = $this->validatedCategoryIds($request, $data['categories'] ?? []);
        unset($data['categories']);

        if (array_key_exists('is_completed', $data)) {
            $data['completed_at'] = $data['is_completed'] ? now() : null;
        }

        $task->update($data);
        $task->categories()->sync($categoryIds);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeOwnership($task);
        $task->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Task deleted.');
    }

    public function toggle(Task $task)
    {
        $this->authorizeOwnership($task);

        $newState = ! $task->is_completed;

        $task->update([
            'is_completed' => $newState,
            'completed_at' => $newState ? now() : null,
        ]);

        return back();
    }

    // --- Helpers ---

    private function validateTask(Request $request, ?Task $task = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_at' => ['nullable', 'date'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];

        // On edit, allow keeping an already-past due date without triggering after_or_equal
        if ($task === null) {
            $rules['due_at'][] = 'after_or_equal:now';
        }

        return $request->validate($rules);
    }

    /**
     * Ensure every submitted category ID belongs to the authenticated user.
     */
    private function validatedCategoryIds(Request $request, array $ids): array
    {
        $ids = array_unique(array_map('intval', $ids));

        if (empty($ids)) {
            return [];
        }

        $owned = Category::where('user_id', $request->user()->id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        if (count($owned) !== count($ids)) {
            throw ValidationException::withMessages([
                'categories' => ['One or more categories are invalid.'],
            ]);
        }

        return $owned;
    }

    private function authorizeOwnership(Task $task): void
    {
        abort_if($task->user_id !== auth()->id(), 404);
    }
}
