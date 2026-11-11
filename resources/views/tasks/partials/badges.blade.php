{{-- Status + priority + due-date badges for one task. Expects $task. --}}
@php
    $statusClass = [
        'pending' => 'bg-amber-100 text-amber-800',
        'in_progress' => 'bg-sky-100 text-sky-800',
        'done' => 'bg-emerald-100 text-emerald-800',
    ][$task->status] ?? 'bg-gray-100 text-gray-700';
    $priorityClass = [
        'low' => 'bg-gray-100 text-gray-700',
        'medium' => 'bg-indigo-100 text-indigo-800',
        'high' => 'bg-rose-100 text-rose-800',
    ][$task->priority] ?? 'bg-gray-100 text-gray-700';
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $task->statusLabel() }}</span>
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $priorityClass }}">{{ $task->priorityLabel() }} priority</span>
@if ($task->due_date)
    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task->isOverdue() ? 'bg-rose-600 text-white' : 'bg-gray-100 text-gray-700' }}">
        {{ $task->isOverdue() ? 'Overdue · ' : 'Due ' }}{{ $task->due_date->format('M j, Y') }}
    </span>
@endif
