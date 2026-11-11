<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One task on the "Tasks for Today" list.
 * Soft delete is done with the is_archived column (not Laravel's SoftDeletes trait),
 * as the activity asks for.
 */
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    /** Allowed values, shared by the validation rules and the forms. */
    public const STATUSES = ['pending' => 'Pending', 'in_progress' => 'In progress', 'done' => 'Done'];

    public const PRIORITIES = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];

    protected $fillable = ['title', 'description', 'status', 'priority', 'due_date', 'is_archived', 'completed_at', 'user_id'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'is_archived' => 'boolean',
        ];
    }

    /** Only tasks that have not been archived (soft-deleted). */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    /** Only archived tasks. */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    /** Past its due date and not finished yet. */
    public function isOverdue(): bool
    {
        return ! $this->isDone() && $this->due_date !== null && $this->due_date->isBefore(today());
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? ucfirst($this->priority);
    }
}
