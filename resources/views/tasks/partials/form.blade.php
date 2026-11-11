{{-- Shared fields for Create and Edit. Expects $task (a new or existing Task). --}}
<div class="space-y-6">
    <div>
        <x-input-label for="title" value="Title *" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" maxlength="120" required autofocus
                      :value="old('title', $task->title)" />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="4" maxlength="1000"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $task->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
        <div>
            <x-input-label for="status" value="Status *" />
            <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                @foreach (\App\Models\Task::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $task->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="priority" value="Priority *" />
            <select id="priority" name="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                @foreach (\App\Models\Task::PRIORITIES as $value => $label)
                    <option value="{{ $value }}" @selected(old('priority', $task->priority) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="due_date" value="Due date" />
            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                          :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
        </div>
    </div>
</div>
