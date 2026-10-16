<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', 'in:all,active,completed'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'search' => ['sometimes', 'string', 'max:255'],
            'overdue' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'in:created_at,due_at,priority'],
            'direction' => ['sometimes', 'in:asc,desc'],
        ]);

        $filters = $request->only(['status', 'priority', 'category_id', 'search', 'overdue']);

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $tasks = $request->user()
            ->tasks()
            ->with('categories:id,name')
            ->filter($filters)
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);

        $this->assertCategoriesBelongToUser($request, $categoryIds);

        $task = $request->user()->tasks()->create($data);

        if ($categoryIds) {
            $task->categories()->sync($categoryIds);
        }

        return response()->json($task->load('categories:id,name'), 201);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        $this->authorizeOwnership($request, $task);

        return response()->json($task->load('categories:id,name'));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorizeOwnership($request, $task);

        $data = $request->validated();
        $categoryIds = $data['categories'] ?? null;
        unset($data['categories']);

        if ($categoryIds !== null) {
            $this->assertCategoriesBelongToUser($request, $categoryIds);
        }

        if (array_key_exists('is_completed', $data)) {
            $data['completed_at'] = $data['is_completed'] ? now() : null;
        }

        $task->update($data);

        if ($categoryIds !== null) {
            $task->categories()->sync($categoryIds);
        }

        return response()->json($task->fresh()->load('categories:id,name'));
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorizeOwnership($request, $task);

        $task->delete();

        return response()->json(null, 204);
    }

    private function authorizeOwnership(Request $request, Task $task): void
    {
        abort_if($task->user_id !== $request->user()->id, 404);
    }

    /**
     * Ensure every category ID belongs to the authenticated user.
     * Prevents user A from tagging a task with user B's category.
     */
    private function assertCategoriesBelongToUser(Request $request, array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $owned = Category::where('user_id', $request->user()->id)
            ->whereIn('id', $categoryIds)
            ->pluck('id')
            ->all();

        $invalid = array_diff($categoryIds, $owned);

        if ($invalid) {
            throw ValidationException::withMessages([
                'categories' => ['One or more categories are invalid.'],
            ]);
        }
    }
}
