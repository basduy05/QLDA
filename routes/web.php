<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
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
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->middleware('admin')
        ->name('admin.users.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
