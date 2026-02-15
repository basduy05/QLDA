<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('notifications.index', [
            'notifications' => $user->notifications()->latest()->paginate(15),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index');
    }
}
