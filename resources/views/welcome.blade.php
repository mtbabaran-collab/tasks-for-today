<x-app-layout>
    <x-slot name="title">Welcome</x-slot>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid gap-10 lg:grid-cols-2 items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Welcome</p>
                <h1 class="mt-2 text-4xl font-bold text-gray-900 leading-tight">Tasks for Today</h1>
                <p class="mt-4 text-lg text-gray-600">
                    A simple place to keep track of what needs to get done today: add tasks, set a priority and a due date,
                    and mark them done. When a task is finished, the system sends an email notification automatically.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-5 py-3 bg-emerald-600 rounded-md font-semibold text-white hover:bg-emerald-700">View the task list</a>
                    @auth
                        <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-5 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">+ New task</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">Log in to manage tasks</a>
                    @endauth
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-800">What you can do</h2>
                <ul class="mt-4 space-y-3 text-gray-600">
                    <li class="flex gap-3"><span class="text-emerald-600 font-bold">✓</span> Anyone can view the Task List, Profile and About pages.</li>
                    <li class="flex gap-3"><span class="text-emerald-600 font-bold">✓</span> Logged-in users can create, edit and delete (archive) tasks.</li>
                    <li class="flex gap-3"><span class="text-emerald-600 font-bold">✓</span> Every form is validated, so empty or wrong values are rejected.</li>
                    <li class="flex gap-3"><span class="text-emerald-600 font-bold">✓</span> Marking a task as done sends a "task completed" email, only once.</li>
                </ul>
            </div>
        </div>
    </section>
</x-app-layout>
