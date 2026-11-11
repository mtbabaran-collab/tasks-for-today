<x-app-layout>
    <x-slot name="title">About</x-slot>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight">About this system</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 space-y-4 text-gray-700">
            <p>
                <strong>Tasks for Today</strong> is a small task management system built with Laravel. It keeps a list of
                tasks with a title, description, status, priority and due date.
            </p>
            <h2 class="text-lg font-semibold text-gray-900">How it works</h2>
            <ul class="list-disc ps-5 space-y-2">
                <li><strong>Viewing</strong>: the Welcome, Task List, Profile and About pages are open to everyone.</li>
                <li><strong>Managing</strong>: creating, editing and deleting tasks requires logging in (Laravel Breeze).</li>
                <li><strong>Soft delete</strong>: deleting a task sets its <code>is_archived</code> column to true. The task stays in the database and can be restored from the Archived page.</li>
                <li><strong>Validation</strong>: the forms check required fields, lengths, allowed status and priority values, and dates.</li>
                <li><strong>Email notification</strong>: when a task's status changes to <em>Done</em>, the system sends a "task completed" email using Laravel's Mail facade. Saving a task that is already done does not send it again.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
