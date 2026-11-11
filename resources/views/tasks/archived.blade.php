<x-app-layout>
    <x-slot name="title">Archived tasks</x-slot>
    <x-slot name="header">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-700 hover:underline">← Back to Task List</a>
        <h1 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">Archived tasks</h1>
        <p class="text-sm text-gray-500">Deleted tasks are only archived (is_archived = true), so they can be restored.</p>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-3">
        @forelse ($tasks as $task)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-gray-800 hover:text-emerald-700">{{ $task->title }}</a>
                    <p class="text-xs text-gray-500">Archived {{ $task->updated_at->diffForHumans() }}</p>
                </div>
                <form method="POST" action="{{ route('tasks.restore', $task) }}">
                    @csrf
                    @method('PATCH')
                    <button class="px-3 py-1.5 rounded-md bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700">Restore</button>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center text-gray-500">No archived tasks.</div>
        @endforelse

        {{ $tasks->links() }}
    </div>
</x-app-layout>
