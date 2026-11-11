<x-app-layout>
    <x-slot name="title">Task List</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Task List</h1>
            @auth
                <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 rounded-md text-sm font-semibold text-white hover:bg-emerald-700">+ New task</a>
            @else
                <p class="text-sm text-gray-500"><a href="{{ route('login') }}" class="text-emerald-700 font-semibold hover:underline">Log in</a> to add, edit or delete tasks.</p>
            @endauth
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Filter by status + search --}}
        <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap items-center gap-3">
            <a href="{{ route('tasks.index', array_filter(['q' => $search])) }}"
               class="px-3 py-1.5 rounded-full text-sm font-medium {{ $status ? 'bg-white text-gray-600 border border-gray-200' : 'bg-gray-800 text-white' }}">
                All ({{ $counts->sum() }})
            </a>
            @foreach (\App\Models\Task::STATUSES as $value => $label)
                <a href="{{ route('tasks.index', array_filter(['status' => $value, 'q' => $search])) }}"
                   class="px-3 py-1.5 rounded-full text-sm font-medium {{ $status === $value ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
                    {{ $label }} ({{ $counts[$value] ?? 0 }})
                </a>
            @endforeach
            <div class="ms-auto flex gap-2">
                @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                <label for="q" class="sr-only">Search tasks</label>
                <input id="q" name="q" value="{{ $search }}" placeholder="Search tasks…" class="rounded-md border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <button class="px-3 py-2 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Search</button>
            </div>
        </form>

        @if ($tasks->isEmpty())
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center text-gray-500">
                No tasks found{{ $search !== '' ? ' for "'.$search.'"' : '' }}.
                @auth <a href="{{ route('tasks.create') }}" class="text-emerald-700 font-semibold hover:underline">Add one</a>. @endauth
            </div>
        @else
            <ul class="space-y-3">
                @foreach ($tasks as $task)
                    <li class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('tasks.show', $task) }}" class="text-lg font-semibold {{ $task->isDone() ? 'text-gray-400 line-through' : 'text-gray-900' }} hover:text-emerald-700">
                                {{ $task->title }}
                            </a>
                            @if ($task->description)
                                <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $task->description }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-2">@include('tasks.partials.badges')</div>
                        </div>
                        @auth
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete (archive) this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-md border border-rose-200 text-sm font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        @endauth
                    </li>
                @endforeach
            </ul>

            {{ $tasks->links() }}
        @endif
    </div>
</x-app-layout>
