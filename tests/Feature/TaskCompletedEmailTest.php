<?php

namespace Tests\Feature;

use App\Mail\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** The "task completed" email: sent once when a task becomes done, never again after. */
class TaskCompletedEmailTest extends TestCase
{
    use RefreshDatabase;

    private function updateTo(User $user, Task $task, string $status): void
    {
        $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => $task->title,
            'description' => $task->description,
            'status' => $status,
            'priority' => $task->priority,
            'due_date' => $task->due_date?->toDateString(),
        ])->assertRedirect(route('tasks.show', $task));
    }

    public function test_marking_a_pending_task_done_sends_one_email(): void
    {
        Mail::fake();
        config(['tasks.notify_email' => 'teacher@example.com']);
        $user = User::factory()->create();
        $task = Task::factory()->create(['status' => 'pending']);

        $this->updateTo($user, $task, 'done');

        Mail::assertSent(TaskCompleted::class, 1);
        Mail::assertSent(TaskCompleted::class, fn (TaskCompleted $mail) => $mail->hasTo('teacher@example.com') && $mail->task->is($task));
        $this->assertNotNull($task->refresh()->completed_at);
    }

    public function test_updating_a_task_that_is_already_done_does_not_resend(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status' => 'pending']);

        $this->updateTo($user, $task, 'done');
        $this->updateTo($user, $task->refresh(), 'done'); // saved again while still done

        Mail::assertSent(TaskCompleted::class, 1);
    }

    public function test_other_status_changes_do_not_send_email(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status' => 'pending']);

        $this->updateTo($user, $task, 'in_progress');

        Mail::assertNothingSent();
    }

    public function test_reopened_task_sends_again_when_done_a_second_time(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $task = Task::factory()->done()->create();

        $this->updateTo($user, $task, 'pending');
        $this->assertNull($task->refresh()->completed_at);
        $this->updateTo($user, $task, 'done');

        Mail::assertSent(TaskCompleted::class, 1);
    }

    public function test_email_content_mentions_the_task(): void
    {
        $task = Task::factory()->done()->create(['title' => 'Submit TSA4']);

        $html = (new TaskCompleted($task, 'Demo User'))->render();

        $this->assertStringContainsString('Submit TSA4', $html);
        $this->assertStringContainsString('Demo User', $html);
    }
}
