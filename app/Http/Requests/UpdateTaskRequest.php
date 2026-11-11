<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Validation\Rule;

/**
 * Validation for the Edit Task form. Same rules as creating, except an existing task
 * may keep a due date that is already in the past (it may simply be overdue).
 */
class UpdateTaskRequest extends StoreTaskRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
