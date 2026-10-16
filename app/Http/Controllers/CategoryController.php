<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = auth()->user()
            ->categories()
            ->withCount('tasks')
            ->orderBy('name')
            ->get();

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'min:1', 'max:50',
                Rule::unique('categories')->where('user_id', auth()->id()),
            ],
        ]);

        $request->user()->categories()->create($validated);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeOwnership($category);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'min:1', 'max:50',
                Rule::unique('categories')
                    ->where('user_id', auth()->id())
                    ->ignore($category->id),
            ],
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category renamed.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeOwnership($category);

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category deleted.');
    }

    private function authorizeOwnership(Category $category): void
    {
        abort_if($category->user_id !== auth()->id(), 404);
    }
}