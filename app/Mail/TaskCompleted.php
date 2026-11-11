<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * "Task completed" notification. Sent from TaskController@update the moment a task's
 * status changes to "done" (never again while it stays done).
 */
class TaskCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Task $task, public ?string $completedBy = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task completed: '.$this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-completed',
        );
    }
}
