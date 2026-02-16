<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\DirectConversation;
use App\Models\DirectMessage;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessengerController extends Controller
{
    public function index()
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        return view('messenger.index', $this->buildBasePayload($user) + [
            'activeType' => null,
            'activeTarget' => null,
            'messages' => collect(),
            'typing' => false,
        ]);
    }

    public function direct(User $contact)
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            return redirect()->route('messenger.index');
        }

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);
        $this->markDirectMessagesSeen($conversation->id, $user->id);

        return view('messenger.index', $this->buildBasePayload($user) + [
            'activeType' => 'direct',
            'activeTarget' => $contact,
            'messages' => $this->getDirectMessages($conversation->id),
            'typing' => $this->isTyping($contact->id, $user->id),
        ]);
    }

    public function group(ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        return view('messenger.index', $this->buildBasePayload($user) + [
            'activeType' => 'group',
            'activeTarget' => $chatGroup,
            'messages' => $this->getGroupMessages($chatGroup->id),
            'typing' => false,
        ]);
    }

    public function directFeed(User $contact)
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            abort(403);
        }

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);
        $this->markDirectMessagesSeen($conversation->id, $user->id);

        return response()->json([
            'html' => view('messenger.partials.direct-feed', [
                'messages' => $this->getDirectMessages($conversation->id),
                'authUserId' => $user->id,
            ])->render(),
            'typing' => $this->isTyping($contact->id, $user->id),
        ]);
    }

    public function groupFeed(ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureGroupAccess($chatGroup);

        return response()->json([
            'html' => view('messenger.partials.group-feed', [
                'messages' => $this->getGroupMessages($chatGroup->id),
                'authUserId' => Auth::id(),
            ])->render(),
        ]);
    }

    public function sendDirect(Request $request, User $contact)
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            return back();
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $body = trim($data['body']);
        if ($body === '') {
            return back()->withErrors(['body' => __('Message cannot be empty.')]);
        }

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);

        if ($this->hasRecentDuplicateDirectMessage($conversation->id, $user->id, $body)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'duplicate' => true]);
            }

            return redirect()->route('messenger.direct', $contact);
        }

        DirectMessage::create([
            'direct_conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $body,
            'seen_at' => null,
        ]);

        $this->emitRealtime(
            channels: ['user.'.$contact->id, 'user.'.$user->id],
            event: 'message.direct',
            payload: [
                'from_id' => $user->id,
                'to_id' => $contact->id,
                'conversation_id' => $conversation->id,
            ]
        );

        $contact->notify(new MessageReceivedNotification(
            __('New message from :name', ['name' => $user->name]),
            $body,
            route('messenger.direct', $user)
        ));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('messenger.direct', $contact);
    }

    public function sendGroup(Request $request, ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $body = trim($data['body']);
        if ($body === '') {
            return back()->withErrors(['body' => __('Message cannot be empty.')]);
        }

        if ($this->hasRecentDuplicateGroupMessage($chatGroup->id, $user->id, $body)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'duplicate' => true]);
            }

            return redirect()->route('messenger.group', $chatGroup);
        }

        ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $user->id,
            'body' => $body,
        ]);

        $memberIds = $chatGroup->members()->pluck('users.id')->all();
        $channels = collect($memberIds)
            ->map(fn ($id) => 'user.'.$id)
            ->push('group.'.$chatGroup->id)
            ->unique()
            ->values()
            ->all();

        $this->emitRealtime(
            channels: $channels,
            event: 'message.group',
            payload: [
                'group_id' => $chatGroup->id,
                'from_id' => $user->id,
            ]
        );

        $chatGroup->members()
            ->where('users.id', '!=', $user->id)
            ->get()
            ->each(function (User $member) use ($chatGroup, $user, $body) {
                $member->notify(new MessageReceivedNotification(
                    __('New group message in :group', ['group' => $chatGroup->name]),
                    __(':name: :message', ['name' => $user->name, 'message' => $body]),
                    route('messenger.group', $chatGroup)
                ));
            });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('messenger.group', $chatGroup);
    }

    public function typing(User $contact)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            return response()->json(['ok' => false], 400);
        }

        Cache::put($this->typingKey($user->id, $contact->id), true, now()->addSeconds(8));

        $this->emitRealtime(
            channels: ['user.'.$contact->id],
            event: 'typing.direct',
            payload: [
                'from_id' => $user->id,
                'to_id' => $contact->id,
            ]
        );

        return response()->json(['ok' => true]);
    }

    private function buildBasePayload(User $user): array
    {
        $groupsQuery = ChatGroup::withCount('messages')->latest();

        if (! $user->isAdmin()) {
            $groupsQuery->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }

        $groups = $groupsQuery->get();

        return [
            'contacts' => User::where('id', '!=', $user->id)->orderBy('name')->get(),
            'groups' => $groups,
            'directMap' => $this->buildDirectMap($user),
            'wsUrl' => (string) config('services.realtime.ws_url', ''),
            'wsUserChannel' => 'user.'.$user->id,
            'wsGroupChannels' => $groups->pluck('id')->map(fn ($id) => 'group.'.$id)->values(),
        ];
    }

    private function buildDirectMap(User $authUser): Collection
    {
        $conversations = DirectConversation::query()
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->where('user_one_id', $authUser->id)
            ->orWhere('user_two_id', $authUser->id)
            ->get();

        $map = collect();

        foreach ($conversations as $conversation) {
            $otherUserId = $conversation->user_one_id === $authUser->id
                ? $conversation->user_two_id
                : $conversation->user_one_id;

            $last = $conversation->messages->first();

            $map->put($otherUserId, [
                'conversation_id' => $conversation->id,
                'last_message' => $last?->body,
                'last_at' => $last?->created_at,
            ]);
        }

        return $map;
    }

    private function findOrCreateConversation(int $authUserId, int $contactUserId): DirectConversation
    {
        [$firstId, $secondId] = $authUserId < $contactUserId
            ? [$authUserId, $contactUserId]
            : [$contactUserId, $authUserId];

        return DirectConversation::firstOrCreate([
            'user_one_id' => $firstId,
            'user_two_id' => $secondId,
        ]);
    }

    private function getDirectMessages(int $conversationId): Collection
    {
        return DirectMessage::where('direct_conversation_id', $conversationId)
            ->with('user')
            ->latest()
            ->take(200)
            ->get()
            ->reverse()
            ->values();
    }

    private function getGroupMessages(int $groupId): Collection
    {
        return ChatMessage::where('chat_group_id', $groupId)
            ->with('user')
            ->latest()
            ->take(200)
            ->get()
            ->reverse()
            ->values();
    }

    private function ensureGroupAccess(ChatGroup $chatGroup): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if (! $chatGroup->members()->where('users.id', $user->id)->exists()) {
            abort(403);
        }
    }

    private function markDirectMessagesSeen(int $conversationId, int $viewerId): void
    {
        DirectMessage::where('direct_conversation_id', $conversationId)
            ->where('user_id', '!=', $viewerId)
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }

    private function isTyping(int $fromUserId, int $toUserId): bool
    {
        return (bool) Cache::get($this->typingKey($fromUserId, $toUserId), false);
    }

    private function typingKey(int $fromUserId, int $toUserId): string
    {
        return 'typing:'.$fromUserId.':'.$toUserId;
    }

    private function purgeExpiredMessages(): void
    {
        $cutoff = now()->subDay();
        DirectMessage::where('created_at', '<', $cutoff)->delete();
        ChatMessage::where('created_at', '<', $cutoff)->delete();
    }

    private function hasRecentDuplicateDirectMessage(int $conversationId, int $userId, string $body): bool
    {
        return DirectMessage::where('direct_conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->where('body', $body)
            ->where('created_at', '>=', now()->subSeconds(3))
            ->exists();
    }

    private function hasRecentDuplicateGroupMessage(int $groupId, int $userId, string $body): bool
    {
        return ChatMessage::where('chat_group_id', $groupId)
            ->where('user_id', $userId)
            ->where('body', $body)
            ->where('created_at', '>=', now()->subSeconds(3))
            ->exists();
    }

    private function emitRealtime(array $channels, string $event, array $payload = []): void
    {
        $endpoint = (string) config('services.realtime.server_url', '');
        $secret = (string) config('services.realtime.secret', '');

        if ($endpoint === '' || $secret === '' || empty($channels)) {
            return;
        }

        try {
            Http::timeout(2)->post(rtrim($endpoint, '/').'/broadcast', [
                'secret' => $secret,
                'channels' => $channels,
                'event' => $event,
                'payload' => $payload,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Realtime emit failed', [
                'event' => $event,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
