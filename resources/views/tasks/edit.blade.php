<x-app-layout>
    <x-slot name="title">Edit task</x-slot>
    <x-slot name="header">
        <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-700 hover:underline">← Back to task</a>
        <h1 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">Edit Task</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8" novalidate>
            @csrf
            @method('PUT')
            @include('tasks.partials.form')
            @unless ($task->isDone())
                <p class="mt-6 text-sm text-gray-500">Changing the status to <strong>Done</strong> sends a "task completed" email.</p>
            @endunless
            <div class="mt-8 flex items-center gap-3">
                <x-primary-button>Save changes</x-primary-button>
                <a href="{{ route('tasks.show', $task) }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
