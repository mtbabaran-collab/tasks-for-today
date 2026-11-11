<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
| Public pages: anyone can view these (no login needed).
*/
Route::view('/', 'welcome')->name('home');
Route::view('/profile', 'pages.profile')->name('profile');
Route::view('/about', 'pages.about')->name('about');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

/*
| Management actions: creating, editing and deleting (archiving) tasks need a login.
| These are registered before /tasks/{task} so "create" and "archived" are not read as a task id.
*/
Route::middleware('auth')->group(function () {
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/archived', [TaskController::class, 'archived'])->name('tasks.archived');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');

    // Breeze account settings (name, email, password). Moved to /account because
    // /profile is the public Profile page.
    Route::get('/account', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/account', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/account', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/tasks/{task}', [TaskController::class, 'show'])->whereNumber('task')->name('tasks.show');

// Breeze sends users to "dashboard" after logging in; the task list is our dashboard.
Route::redirect('/dashboard', '/tasks')->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';
