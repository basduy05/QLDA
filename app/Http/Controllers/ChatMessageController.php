<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use App\Services\RealtimeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function store(Request $request, ChatGroup $chatGroup, RealtimeService $realtime): RedirectResponse
    {
        $this->purgeExpiredMessages();
        $this->ensureAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $user->id,
            'body' => trim($data['body']),
        ]);
        
        // Broadcast
        $realtime->broadcast(['chat_group.'.$chatGroup->id], 'message.new', [
            'id' => $message->id,
            'chat_group_id' => $chatGroup->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
        ]);

        $chatGroup->members()
            ->where('users.id', '!=', $user->id)
            ->get()
            ->each(function (User $member) use ($chatGroup, $user, $data, $realtime) {
                $member->notify(new MessageReceivedNotification(
                    __('New group message in :group', ['group' => $chatGroup->name]),
                    __(':name: :message', ['name' => $user->name, 'message' => trim($data['body'])]),
                    route('messenger.group', $chatGroup)
                ));
                $realtime->broadcast('user.'.$member->id, 'notification.new', []);
            });

        return redirect()->route('messenger.group', $chatGroup)
            ->with('status', __('Message sent.'));
    }

    private function ensureAccess(ChatGroup $group): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if (! $group->members()->where('users.id', $user->id)->exists()) {
            abort(403);
        }
    }

    private function purgeExpiredMessages(): void
    {
        ChatMessage::where('created_at', '<', now()->subDay())->delete();
    }
}
