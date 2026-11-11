<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Public pages, login protection, validation and soft delete (is_archived). */
class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_viewable_by_guests(): void
    {
        $task = Task::factory()->create(['title' => 'Visible task']);

        $this->get('/')->assertOk()->assertSee('Tasks for Today');
        $this->get('/tasks')->assertOk()->assertSee('Visible task');
        $this->get('/profile')->assertOk()->assertSee('Profile');
        $this->get('/about')->assertOk()->assertSee('About');
        $this->get(route('tasks.show', $task))->assertOk()->assertSee('Visible task');
    }

    public function test_guests_cannot_create_edit_or_delete_tasks(): void
    {
        $task = Task::factory()->create();

        $this->get(route('tasks.create'))->assertRedirect(route('login'));
        $this->post(route('tasks.store'), ['title' => 'Sneaky', 'status' => 'pending', 'priority' => 'low'])->assertRedirect(route('login'));
        $this->get(route('tasks.edit', $task))->assertRedirect(route('login'));
        $this->put(route('tasks.update', $task), ['title' => 'Changed', 'status' => 'done', 'priority' => 'low'])->assertRedirect(route('login'));
        $this->delete(route('tasks.destroy', $task))->assertRedirect(route('login'));

        $this->assertDatabaseMissing('tasks', ['title' => 'Sneaky']);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => $task->title, 'is_archived' => false]);
    }

    public function test_logged_in_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Write the README',
            'description' => 'Explain the mail setup.',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDay()->toDateString(),
        ]);

        $task = Task::firstWhere('title', 'Write the README');
        $this->assertNotNull($task);
        $this->assertSame($user->id, $task->user_id);
        $response->assertRedirect(route('tasks.show', $task));
    }

    public function test_create_form_rejects_bad_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tasks.store'), [
            'title' => '   ',
            'status' => 'finished',
            'priority' => 'urgent',
            'due_date' => now()->subDay()->toDateString(),
        ])->assertSessionHasErrors(['title', 'status', 'priority', 'due_date']);

        $this->actingAs($user)->post(route('tasks.store'), [
            'title' => str_repeat('x', 121),
            'status' => 'pending',
            'priority' => 'low',
        ])->assertSessionHasErrors(['title']);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_logged_in_user_can_update_a_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['title' => 'Old title']);

        $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'New title',
            'description' => 'Updated',
            'status' => 'in_progress',
            'priority' => 'low',
            'due_date' => '',
        ])->assertRedirect(route('tasks.show', $task));

        $task->refresh();
        $this->assertSame('New title', $task->title);
        $this->assertSame('in_progress', $task->status);
        $this->assertNull($task->due_date);
    }

    public function test_delete_archives_the_task_instead_of_removing_it(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['title' => 'Archive me']);

        $this->actingAs($user)->delete(route('tasks.destroy', $task))->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_archived' => true]);
        // The list no longer contains it (checked on the data, since the flash message also names it).
        $this->get('/tasks')->assertViewHas('tasks', fn ($tasks) => collect($tasks->items())->doesntContain('id', $task->id));
        $this->actingAs($user)->get(route('tasks.archived'))->assertSee('Archive me');
    }

    public function test_archived_task_can_be_restored(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->archived()->create(['title' => 'Bring me back']);

        $this->actingAs($user)->patch(route('tasks.restore', $task))->assertRedirect(route('tasks.show', $task));

        $this->assertFalse($task->refresh()->is_archived);
        $this->get('/tasks')->assertSee('Bring me back');
    }

    public function test_guests_cannot_open_an_archived_task(): void
    {
        $task = Task::factory()->archived()->create();

        $this->get(route('tasks.show', $task))->assertNotFound();
    }

    public function test_task_list_filters_by_status(): void
    {
        Task::factory()->create(['title' => 'Still pending']);
        Task::factory()->done()->create(['title' => 'Already finished']);

        $this->get('/tasks?status=done')->assertSee('Already finished')->assertDontSee('Still pending');
    }
}
