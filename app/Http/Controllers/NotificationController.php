<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->notifications()
            ->where('created_at', '<', now()->subHours(36))
            ->delete();

        return view('notifications.index', [
            'notifications' => $user->notifications()->latest()->paginate(15),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->notifications()
            ->where('created_at', '<', now()->subHours(36))
            ->delete();

        $user->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index');
    }

    public function pulse()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->notifications()
            ->where('created_at', '<', now()->subHours(36))
            ->delete();

        $unread = $user->unreadNotifications()->latest()->limit(5)->get();

        return response()->json([
            'unreadCount' => $user->unreadNotifications()->count(),
            'items' => $unread->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? __('Notification'),
                    'message' => $notification->data['message'] ?? '',
                    'url' => $notification->data['url'] ?? route('notifications.index'),
                    'created_at' => $notification->created_at->format('d/m H:i'),
                ];
            }),
        ]);
    }
}
