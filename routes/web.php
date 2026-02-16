<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/lang/{locale}', function (string $locale) {
    if (!in_array($locale, ['vi', 'en'], true)) {
        abort(404);
    }

    session(['locale' => $locale]);

    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['locale' => $locale]);
    }

    return back();
})->name('lang.switch');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)->shallow();
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])
        ->name('tasks.comments.store');
    Route::get('/exports/projects', [ExportController::class, 'projects'])->name('exports.projects');
    Route::get('/exports/tasks', [ExportController::class, 'tasks'])->name('exports.tasks');
    Route::get('/exports/reports', [ExportController::class, 'reports'])->name('exports.reports');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read');
    Route::get('/chat-groups', [ChatGroupController::class, 'index'])->name('chat-groups.index');
    Route::post('/chat-groups', [ChatGroupController::class, 'store'])->name('chat-groups.store');
    Route::get('/chat-groups/{chatGroup}', [ChatGroupController::class, 'show'])->name('chat-groups.show');
    Route::get('/chat-groups/{chatGroup}/messages', [ChatGroupController::class, 'messages'])
        ->name('chat-groups.messages.index');
    Route::post('/chat-groups/{chatGroup}/messages', [ChatMessageController::class, 'store'])
        ->name('chat-groups.messages.store');
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->middleware('admin')
        ->name('admin.users.index');
    Route::patch('/admin/users/{user}', [AdminUserController::class, 'update'])
        ->middleware('admin')
        ->name('admin.users.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
