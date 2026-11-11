<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Sample "tasks for today" so the list is not empty on a fresh install. */
class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::first();

        $tasks = [
            ['Finish IT0049 TSA4 write-up', 'Push the Laravel project to GitHub and paste the hosted link in the LMS.', 'in_progress', 'high', 0],
            ['Review for the IT0093 demo', 'Practice explaining the code and run the tests on the school Mac.', 'pending', 'high', 1],
            ['Buy printer ink', 'Black ink for the documentation printouts.', 'pending', 'low', 2],
            ['Group meeting at 4 PM', 'Library 3rd floor. Bring the laptop and charger.', 'pending', 'medium', 0],
            ['Submit enrollment form', 'Registrar window 2, bring a valid ID.', 'pending', 'medium', 3],
            ['Read Laravel Mail docs', 'Mailables, Blade email views and the log mail driver.', 'done', 'medium', null],
            ['Clean up GitHub repos', 'Archive old practice projects.', 'done', 'low', null],
            ['Pay the internet bill', 'Due at the end of the week.', 'pending', 'high', 5],
        ];

        foreach ($tasks as [$title, $description, $status, $priority, $dueInDays]) {
            Task::create([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'due_date' => $dueInDays === null ? null : now()->addDays($dueInDays)->toDateString(),
                'completed_at' => $status === 'done' ? now()->subDay() : null,
                'user_id' => $owner?->id,
            ]);
        }

        // One archived (soft-deleted) task, to show the Archived page works.
        Task::create([
            'title' => 'Old reminder (archived)',
            'description' => 'Archived with the Delete button, still kept in the database.',
            'status' => 'pending',
            'priority' => 'low',
            'is_archived' => true,
            'user_id' => $owner?->id,
        ]);
    }
}
