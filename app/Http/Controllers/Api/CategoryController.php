<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = $request->user()
            ->categories()
            ->withCount('tasks')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $request->user()->categories()->create($request->validated());

        return response()->json($category, 201);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        $this->authorizeOwnership($request, $category);

        return response()->json($category->load('tasks'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorizeOwnership($request, $category);

        $category->update($request->validated());

        return response()->json($category);
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $this->authorizeOwnership($request, $category);

        $category->delete();

        return response()->json(null, 204);
    }

    private function authorizeOwnership(Request $request, Category $category): void
    {
        abort_if($category->user_id !== $request->user()->id, 404);
    }
}
