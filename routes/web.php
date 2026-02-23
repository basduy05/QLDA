<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\CallSessionController;
use App\Http\Controllers\ChatGroupController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\MessengerTermsController;
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
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])
        ->name('projects.members.add');
    Route::patch('/projects/{project}/members/{member}', [ProjectController::class, 'updateMemberRole'])
        ->name('projects.members.update');
    Route::delete('/projects/{project}/members/{member}', [ProjectController::class, 'removeMember'])
        ->name('projects.members.remove');
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
    Route::get('/notifications/pulse', [NotificationController::class, 'pulse'])
        ->name('notifications.pulse');
    Route::get('/messenger/terms', [MessengerTermsController::class, 'show'])
        ->name('messenger.terms.show');
    Route::post('/messenger/terms/accept', [MessengerTermsController::class, 'accept'])
        ->name('messenger.terms.accept');
    Route::post('/messenger/terms/decline', [MessengerTermsController::class, 'decline'])
        ->name('messenger.terms.decline');

    Route::middleware('messenger.terms')->group(function () {
    Route::get('/messenger', [MessengerController::class, 'index'])->name('messenger.index');
    Route::get('/messenger/direct/{contact}', [MessengerController::class, 'direct'])->name('messenger.direct');
    Route::get('/messenger/group/{chatGroup}', [MessengerController::class, 'group'])->name('messenger.group');
    Route::get('/messenger/direct/{contact}/feed', [MessengerController::class, 'directFeed'])->name('messenger.direct-feed');
    Route::get('/messenger/group/{chatGroup}/feed', [MessengerController::class, 'groupFeed'])->name('messenger.group-feed');
    Route::post('/messenger/direct/{contact}', [MessengerController::class, 'sendDirect'])->name('messenger.send-direct');
    Route::post('/messenger/group/{chatGroup}', [MessengerController::class, 'sendGroup'])->name('messenger.send-group');
    Route::patch('/messenger/group/{chatGroup}/name', [MessengerController::class, 'updateGroupName'])->name('messenger.group.rename');
    Route::patch('/messenger/group/{chatGroup}/members', [MessengerController::class, 'updateGroupMembers'])->name('messenger.group.members');
    Route::patch('/messenger/group/{chatGroup}/members/{member}/nickname', [MessengerController::class, 'updateGroupMemberNickname'])->name('messenger.group.member-nickname');
    Route::post('/messenger/direct/{contact}/typing', [MessengerController::class, 'typing'])->name('messenger.typing');
    });
    Route::post('/calls/start/{contact}', [CallSessionController::class, 'start'])->name('calls.start');
    Route::get('/calls/poll', [CallSessionController::class, 'poll'])->name('calls.poll');
    Route::get('/calls/{callSession}', [CallSessionController::class, 'show'])->name('calls.show');
    Route::post('/calls/{callSession}/accept', [CallSessionController::class, 'accept'])->name('calls.accept');
    Route::post('/calls/{callSession}/reject', [CallSessionController::class, 'reject'])->name('calls.reject');
    Route::post('/calls/{callSession}/end', [CallSessionController::class, 'end'])->name('calls.end');
    Route::post('/calls/{callSession}/signal', [CallSessionController::class, 'signal'])->name('calls.signal');
    Route::get('/messages', [DirectMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{contact}', [DirectMessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{contact}/feed', [DirectMessageController::class, 'feed'])->name('messages.feed');
    Route::post('/messages/{contact}', [DirectMessageController::class, 'store'])->name('messages.store');
    Route::get('/chat-groups', [ChatGroupController::class, 'index'])->name('chat-groups.index');
    Route::post('/chat-groups', [ChatGroupController::class, 'store'])->name('chat-groups.store');
    Route::get('/chat-groups/{chatGroup}', [ChatGroupController::class, 'show'])->name('chat-groups.show');
    Route::post('/chat-groups/{chatGroup}/nickname', [ChatGroupController::class, 'updateNickname'])
        ->name('chat-groups.nickname');
    Route::delete('/chat-groups/{chatGroup}', [ChatGroupController::class, 'destroy'])
        ->name('chat-groups.destroy');
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
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])
        ->middleware('admin')
        ->name('admin.users.destroy');
    Route::get('/admin/settings/ai', [AdminSettingController::class, 'editAi'])
        ->middleware('admin')
        ->name('admin.settings.ai.edit');
    Route::patch('/admin/settings/ai', [AdminSettingController::class, 'updateAi'])
        ->middleware('admin')
        ->name('admin.settings.ai.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
