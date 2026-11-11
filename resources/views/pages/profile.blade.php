<x-app-layout>
    <x-slot name="title">Profile</x-slot>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-5">
                <div class="h-16 w-16 rounded-full bg-emerald-600 text-white flex items-center justify-center text-2xl font-bold">MB</div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Mark Anthony Babaran</h2>
                    <p class="text-gray-600">Student, FEU Institute of Technology</p>
                </div>
            </div>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Course</dt>
                    <dd class="mt-1 font-medium text-gray-800">IT0049 Web System Technologies</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Activity</dt>
                    <dd class="mt-1 font-medium text-gray-800">Technical Summative Assessment 4</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Built with</dt>
                    <dd class="mt-1 font-medium text-gray-800">Laravel {{ app()->version() }}, Breeze, Blade, Tailwind CSS</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Tasks on the list</dt>
                    <dd class="mt-1 font-medium text-gray-800">{{ \App\Models\Task::active()->count() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>
