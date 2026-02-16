<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    public function store(Request $request, ChatGroup $chatGroup): RedirectResponse
    {
        $this->purgeExpiredMessages();
        $this->ensureAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => Auth::id(),
            'body' => trim($data['body']),
        ]);

        $chatGroup->members()
            ->where('users.id', '!=', $user->id)
            ->get()
            ->each(function (User $member) use ($chatGroup, $user, $data) {
                $member->notify(new MessageReceivedNotification(
                    __('New group message in :group', ['group' => $chatGroup->name]),
                    __(':name: :message', ['name' => $user->name, 'message' => trim($data['body'])]),
                    route('messenger.group', $chatGroup)
                ));
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
