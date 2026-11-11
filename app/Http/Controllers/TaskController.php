<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Tasks CRUD.
 * - index and show are public (anyone can view the list).
 * - create, store, edit, update, destroy (archive), archived and restore need a login
 *   (see the "auth" middleware group in routes/web.php).
 */
class TaskController extends Controller
{
    /** Public task list with an optional status filter and search. Archived tasks are hidden. */
    public function index(Request $request): View
    {
        // Only accept plain text values (a crafted ?q[]=x would otherwise cause an error).
        $status = is_string($request->query('status')) && array_key_exists($request->query('status'), Task::STATUSES)
            ? $request->query('status')
            : null;
        $search = is_string($request->query('q')) ? mb_substr(trim($request->query('q')), 0, 100) : '';
        // % and _ are LIKE wildcards; escape them with "!" so they are searched as normal characters
        // ("!" works the same in SQLite and MySQL, unlike a backslash).
        $like = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';

        $tasks = Task::active()
            ->when($status !== null, fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($like) {
                $q->whereRaw("title like ? escape '!'", [$like])->orWhereRaw("description like ? escape '!'", [$like]);
            }))
            // Unfinished first, then by due date (no date last), newest first.
            ->orderByRaw("case when status = 'done' then 1 else 0 end")
            ->orderByRaw('case when due_date is null then 1 else 0 end')
            ->orderBy('due_date')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = Task::active()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('tasks.index', compact('tasks', 'status', 'search', 'counts'));
    }

    public function show(Task $task): View
    {
        abort_if($task->is_archived && ! auth()->check(), 404);

        return view('tasks.show', compact('task'));
    }

    public function create(): View
    {
        return view('tasks.create', ['task' => new Task(['status' => 'pending', 'priority' => 'medium'])]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['completed_at'] = $data['status'] === 'done' ? now() : null;

        $task = Task::create($data);

        return redirect()->route('tasks.show', $task)->with('status', 'Task created.');
    }

    public function edit(Task $task): View
    {
        abort_if($task->is_archived, 404);

        return view('tasks.edit', compact('task'));
    }

    /**
     * Saves the edit form. If the status changes from something else to "done",
     * the "task completed" email is sent once. Saving a task that is already done
     * does not send it again, because the old status was already "done".
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        abort_if($task->is_archived, 404);

        $data = $request->validated();
        $justCompleted = false;

        if ($data['status'] === 'done') {
            // Move to "done" only if the row is not done yet. The WHERE makes this a single atomic step,
            // so even two quick saves at the same time produce exactly one "just completed" (one email).
            $justCompleted = Task::whereKey($task->id)
                ->where('status', '!=', 'done')
                ->update(['status' => 'done', 'completed_at' => now()]) === 1;
            $task->refresh();
            unset($data['status']);
        } else {
            $data['completed_at'] = null; // re-opened or still in progress
        }

        $task->update($data);

        $message = 'Task updated.';
        if ($justCompleted) {
            $message = $this->sendCompletedEmail($task, $request->user()->name)
                ? 'Task marked as done. A completion email was sent to '.config('tasks.notify_email').'.'
                : 'Task marked as done, but the completion email could not be sent (see the log).';
        }

        return redirect()->route('tasks.show', $task)->with('status', $message);
    }

    /** Soft delete: the task is archived (is_archived = true), not removed from the database. */
    public function destroy(Task $task): RedirectResponse
    {
        $task->update(['is_archived' => true]);

        return redirect()->route('tasks.index')->with('status', "\"{$task->title}\" was archived.");
    }

    /** Logged-in users can see archived tasks and bring them back. */
    public function archived(): View
    {
        $tasks = Task::archived()->latest('updated_at')->paginate(10);

        return view('tasks.archived', compact('tasks'));
    }

    public function restore(Task $task): RedirectResponse
    {
        $task->update(['is_archived' => false]);

        return redirect()->route('tasks.show', $task)->with('status', 'Task restored.');
    }

    /** Uses Laravel's Mail facade. A mail error must not break saving the task. */
    private function sendCompletedEmail(Task $task, ?string $completedBy): bool
    {
        try {
            Mail::to(config('tasks.notify_email'))->send(new TaskCompleted($task, $completedBy));

            return true;
        } catch (\Throwable $e) {
            Log::error('Task completed email failed: '.$e->getMessage(), ['task_id' => $task->id]);

            return false;
        }
    }
}
