<x-app-layout>
    <x-slot name="title">New task</x-slot>
    <x-slot name="header">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-700 hover:underline">← Back to Task List</a>
        <h1 class="mt-2 font-semibold text-xl text-gray-800 leading-tight">Create Task</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('tasks.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8" novalidate>
            @csrf
            @include('tasks.partials.form')
            <div class="mt-8 flex items-center gap-3">
                <x-primary-button>Save task</x-primary-button>
                <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
