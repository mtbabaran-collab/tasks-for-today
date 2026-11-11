<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tasks table. "Deleting" a task only sets is_archived = true (soft delete),
     * so nothing is ever removed from the database by the app.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');     // pending | in_progress | done
            $table->string('priority', 10)->default('medium');    // low | medium | high
            $table->date('due_date')->nullable();
            $table->boolean('is_archived')->default(false);       // soft delete flag
            $table->timestamp('completed_at')->nullable();        // set when the task becomes "done"
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // who created it
            $table->timestamps();

            $table->index(['is_archived', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
