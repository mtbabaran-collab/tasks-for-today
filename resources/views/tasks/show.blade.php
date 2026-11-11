<x-app-layout>
    <x-slot name="title">{{ $task->title }}</x-slot>
    <x-slot name="header">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-700 hover:underline">← Back to Task List</a>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <article class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            @if ($task->is_archived)
                <p class="mb-4 rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-600">This task is archived (soft-deleted).</p>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
            <div class="mt-3 flex flex-wrap gap-2">@include('tasks.partials.badges')</div>

            <div class="mt-6 text-gray-700 whitespace-pre-line">{{ $task->description ?: 'No description.' }}</div>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-gray-500">Created</dt><dd class="font-medium text-gray-800">{{ $task->created_at->format('M j, Y g:i A') }}{{ $task->user ? ' by '.$task->user->name : '' }}</dd></div>
                <div><dt class="text-gray-500">Last updated</dt><dd class="font-medium text-gray-800">{{ $task->updated_at->format('M j, Y g:i A') }}</dd></div>
                @if ($task->completed_at)
                    <div><dt class="text-gray-500">Completed</dt><dd class="font-medium text-gray-800">{{ $task->completed_at->format('M j, Y g:i A') }}</dd></div>
                @endif
            </dl>

            @auth
                <div class="mt-8 flex flex-wrap gap-3 border-t border-gray-100 pt-6">
                    @if ($task->is_archived)
                        <form method="POST" action="{{ route('tasks.restore', $task) }}">
                            @csrf
                            @method('PATCH')
                            <button class="px-4 py-2 rounded-md bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700">Restore task</button>
                        </form>
                    @else
                        <a href="{{ route('tasks.edit', $task) }}" class="px-4 py-2 rounded-md bg-gray-800 text-sm font-semibold text-white hover:bg-gray-700">Edit task</a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete (archive) this task?');">
                            @csrf
                            @method('DELETE')
                            <button class="px-4 py-2 rounded-md border border-rose-200 text-sm font-semibold text-rose-700 hover:bg-rose-50">Delete</button>
                        </form>
                    @endif
                </div>
            @endauth
        </article>
    </div>
</x-app-layout>
