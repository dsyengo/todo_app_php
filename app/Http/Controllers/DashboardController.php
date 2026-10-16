<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'status' => ['sometimes', 'in:all,active,completed,overdue'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'search' => ['sometimes', 'string', 'max:255'],
            'sort' => ['sometimes', 'in:created_at,due_at,priority'],
            'direction' => ['sometimes', 'in:asc,desc'],
        ]);

        $user = $request->user();
        $status = $request->input('status', 'active');
        $filters = $request->only(['priority', 'category_id', 'search']);

        // Stats — always computed from the full task list (unaffected by filters)
        $stats = $this->stats($user);

        // Filtered task list
        $tasks = $user->tasks()
            ->with('categories:id,name')
            ->filter(['status' => $status] + $filters)
            ->orderBy(
                $request->input('sort', 'created_at'),
                $request->input('direction', 'desc')
            )
            ->paginate(15)
            ->withQueryString();

        $categories = $user->categories()->orderBy('name')->get();

        return view('dashboard', [
            'tasks' => $tasks,
            'categories' => $categories,
            'stats' => $stats,
            'status' => $status,
            'filters' => $filters,
            'hasFilters' => collect($filters)->filter()->isNotEmpty(),
        ]);
    }

    private function stats($user): array
    {
        $base = $user->tasks();

        $total = (clone $base)->count();
        $completed = (clone $base)->where('is_completed', true)->count();
        $pending = $total - $completed;

        $overdue = (clone $base)
            ->where('is_completed', false)
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        $dueToday = (clone $base)
            ->where('is_completed', false)
            ->whereNotNull('due_at')
            ->whereDate('due_at', today())
            ->count();

        $dueThisWeek = (clone $base)
            ->where('is_completed', false)
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays(7)])
            ->count();

        $completionRate = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'pending' => $pending,
            'completed' => $completed,
            'overdue' => $overdue,
            'due_today' => $dueToday,
            'due_this_week' => $dueThisWeek,
            'completion_rate' => $completionRate,
        ];
    }
}
