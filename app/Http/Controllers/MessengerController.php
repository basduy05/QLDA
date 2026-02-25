<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\DirectConversation;
use App\Models\DirectMessage;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'groupMembers' => collect(),
            'groupNicknames' => [],
            'myGroupNickname' => null,
            'canEditNickname' => false,
            'canDeleteGroup' => false,
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
            'groupMembers' => collect(),
            'groupNicknames' => [],
            'myGroupNickname' => null,
            'canEditNickname' => false,
            'canDeleteGroup' => false,
        ]);
    }

    public function group(ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $chatGroup->load(['members']);
        $myMember = $chatGroup->members->firstWhere('id', $user->id);
        $canEditNickname = (bool) $myMember;

        return view('messenger.index', $this->buildBasePayload($user) + [
            'activeType' => 'group',
            'activeTarget' => $chatGroup,
            'messages' => $this->getGroupMessages($chatGroup->id),
            'typing' => false,
            'groupMembers' => $chatGroup->members->sortBy('name')->values(),
            'groupNicknames' => $this->getGroupNicknames($chatGroup),
            'myGroupNickname' => $myMember?->pivot?->nickname,
            'canEditNickname' => $canEditNickname,
            'canDeleteGroup' => $user->isAdmin() || (int) $chatGroup->created_by === (int) $user->id,
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

    public function search(Request $request)
    {
        $query = trim((string) $request->get('query'));
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Allow searching by exact email or partial name/email
        // Limit to 20 results
        $results = User::where('id', '!=', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('email', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->take(20)
            ->get(['id', 'name', 'email']); // Return minimal data

        // If restriction is ON, maybe we should indicate if they are in project or not?
        // But the requirement says "Allow searching by email". So we return them regardless.

        return response()->json($results);
    }

    public function groupFeed(ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureGroupAccess($chatGroup);

        return response()->json([
            'html' => view('messenger.partials.group-feed', [
                'messages' => $this->getGroupMessages($chatGroup->id),
                'authUserId' => Auth::id(),
                'nicknames' => $this->getGroupNicknames($chatGroup),
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
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xlsx,xls,ppt,pptx,txt,zip,rar'],
        ]);

        $body = trim((string) ($data['body'] ?? ''));
        $attachment = $request->file('attachment');

        if ($body === '' && ! $attachment) {
            return back()->withErrors(['body' => __('Message cannot be empty.')]);
        }

        $attachmentPayload = $attachment ? $this->storeAttachment($attachment) : [];

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);

        if ($body !== '' && ! $attachment && $this->hasRecentDuplicateDirectMessage($conversation->id, $user->id, $body)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'duplicate' => true]);
            }

            return redirect()->route('messenger.direct', $contact);
        }

        DirectMessage::create([
            'direct_conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $body,
            'attachment_path' => $attachmentPayload['attachment_path'] ?? null,
            'attachment_name' => $attachmentPayload['attachment_name'] ?? null,
            'attachment_size' => $attachmentPayload['attachment_size'] ?? null,
            'attachment_mime' => $attachmentPayload['attachment_mime'] ?? null,
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
            $this->notificationPreview($body, ! empty($attachmentPayload)),
            route('messenger.direct', $user)
        ));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('messenger.direct', ['contact' => $contact, 'popup' => $request->query('popup')]);
    }

    public function sendGroup(Request $request, ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xlsx,xls,ppt,pptx,txt,zip,rar'],
        ]);

        $body = trim((string) ($data['body'] ?? ''));
        $attachment = $request->file('attachment');

        if ($body === '' && ! $attachment) {
            return back()->withErrors(['body' => __('Message cannot be empty.')]);
        }

        $attachmentPayload = $attachment ? $this->storeAttachment($attachment) : [];

        if ($body !== '' && ! $attachment && $this->hasRecentDuplicateGroupMessage($chatGroup->id, $user->id, $body)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'duplicate' => true]);
            }

            return redirect()->route('messenger.group', $chatGroup);
        }

        ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $user->id,
            'body' => $body,
            'attachment_path' => $attachmentPayload['attachment_path'] ?? null,
            'attachment_name' => $attachmentPayload['attachment_name'] ?? null,
            'attachment_size' => $attachmentPayload['attachment_size'] ?? null,
            'attachment_mime' => $attachmentPayload['attachment_mime'] ?? null,
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
            ->each(function (User $member) use ($chatGroup, $user, $body, $attachmentPayload) {
                $member->notify(new MessageReceivedNotification(
                    __('New group message in :group', ['group' => $chatGroup->name]),
                    __(':name: :message', ['name' => $user->name, 'message' => $this->notificationPreview($body, ! empty($attachmentPayload))]),
                    route('messenger.group', $chatGroup)
                ));
            });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('messenger.group', ['chatGroup' => $chatGroup, 'popup' => $request->query('popup')]);
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

    public function updateGroupName(Request $request, ChatGroup $chatGroup)
    {
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $newName = trim($data['name']);
        if ($newName === '' || $newName === $chatGroup->name) {
            return back();
        }

        $oldName = $chatGroup->name;
        $chatGroup->forceFill(['name' => $newName])->save();

        $this->createGroupSystemMessage(
            $chatGroup,
            __(':user renamed group from ":old" to ":new"', [
                'user' => $user->name,
                'old' => $oldName,
                'new' => $newName,
            ])
        );

        return redirect()->route('messenger.group', ['chatGroup' => $chatGroup, 'popup' => $request->query('popup')])
            ->with('status', __('Group name updated.'));
    }

    public function updateGroupMemberNickname(Request $request, ChatGroup $chatGroup, User $member)
    {
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        if (! $chatGroup->members()->where('users.id', $member->id)->exists()) {
            abort(404);
        }

        $data = $request->validate([
            'nickname' => ['nullable', 'string', 'max:40'],
        ]);

        $oldNickname = trim((string) $chatGroup->members()
            ->where('users.id', $member->id)
            ->first()?->pivot?->nickname);

        $newNickname = trim((string) ($data['nickname'] ?? ''));
        $chatGroup->members()->updateExistingPivot($member->id, [
            'nickname' => $newNickname !== '' ? $newNickname : null,
        ]);

        if ($oldNickname !== $newNickname) {
            $displayOld = $oldNickname !== '' ? $oldNickname : $member->name;
            $displayNew = $newNickname !== '' ? $newNickname : $member->name;

            $this->createGroupSystemMessage(
                $chatGroup,
                __(':user changed nickname of :member from ":old" to ":new"', [
                    'user' => $user->name,
                    'member' => $member->name,
                    'old' => $displayOld,
                    'new' => $displayNew,
                ])
            );
        }

        return redirect()->route('messenger.group', ['chatGroup' => $chatGroup, 'popup' => $request->query('popup')])
            ->with('status', __('Group nickname updated.'));
    }

    public function updateGroupMembers(Request $request, ChatGroup $chatGroup)
    {
        $this->ensureGroupAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $oldMemberIds = $chatGroup->members()->pluck('users.id')->map(fn ($id) => (int) $id);

        $newMemberIds = collect($data['member_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->push((int) $user->id)
            ->unique()
            ->values();

        $addedIds = $newMemberIds->diff($oldMemberIds)->values();
        $removedIds = $oldMemberIds->diff($newMemberIds)->values();

        $chatGroup->members()->sync($newMemberIds->all());

        if ($addedIds->isNotEmpty()) {
            $addedNames = User::whereIn('id', $addedIds->all())
                ->orderBy('name')
                ->pluck('name')
                ->join(', ');

            $this->createGroupSystemMessage(
                $chatGroup,
                __(':user added member(s): :members', [
                    'user' => $user->name,
                    'members' => $addedNames,
                ])
            );
        }

        if ($removedIds->isNotEmpty()) {
            $removedNames = User::whereIn('id', $removedIds->all())
                ->orderBy('name')
                ->pluck('name')
                ->join(', ');

            $this->createGroupSystemMessage(
                $chatGroup,
                __(':user removed member(s): :members', [
                    'user' => $user->name,
                    'members' => $removedNames,
                ])
            );
        }

        return redirect()->route('messenger.group', ['chatGroup' => $chatGroup, 'popup' => $request->query('popup')])
            ->with('status', __('Group members updated.'));
    }

    private function buildBasePayload(User $user): array
    {
        // Eager load creator and count members to avoid N+1 in the group manager modal
        $groupsQuery = ChatGroup::with(['creator'])->withCount(['messages', 'members'])->latest();

        if (! $user->isAdmin()) {
            $groupsQuery->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }

        $groups = $groupsQuery->get();

        $contactsQuery = User::where('id', '!=', $user->id)->orderBy('name');

        if (! $user->isAdmin() && AppSetting::getValue('messenger.project_members_only', false)) {
            $myProjectIds = \Illuminate\Support\Facades\DB::table('project_members')
                ->where('user_id', $user->id) 
                ->pluck('project_id')
                ->merge(\Illuminate\Support\Facades\DB::table('projects')->where('owner_id', $user->id)->pluck('id'))
                ->unique();

            $contactsQuery->where(function ($q) use ($myProjectIds) {
                // Users who are members of my projects
                $q->whereHas('projects', fn ($dq) => $dq->whereIn('projects.id', $myProjectIds))
                  // Or users who own my projects
                  ->orWhereHas('projectsOwned', fn ($dq) => $dq->whereIn('projects.id', $myProjectIds));
            });
        }

        return [
            'contacts' => $contactsQuery->get(),
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
                'last_message' => $last ? $this->notificationPreview((string) $last->body, ! empty($last->attachment_path)) : null,
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
            Http::connectTimeout(1)->timeout(1)->post(rtrim($endpoint, '/').'/broadcast', [
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

    private function createGroupSystemMessage(ChatGroup $chatGroup, string $message): void
    {
        ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => null,
            'body' => $message,
            'is_system' => true,
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
            ]
        );
    }

    private function getGroupNicknames(ChatGroup $chatGroup): array
    {
        if (! $chatGroup->relationLoaded('members')) {
            $chatGroup->load('members');
        }

        return $chatGroup->members
            ->mapWithKeys(function (User $member) {
                $nickname = trim((string) ($member->pivot->nickname ?? ''));
                return [$member->id => $nickname !== '' ? $nickname : $member->name];
            })
            ->all();
    }

    private function storeAttachment(UploadedFile $attachment): array
    {
        $original = trim((string) $attachment->getClientOriginalName());

        return [
            'attachment_path' => $attachment->store('messenger-attachments', 'public'),
            'attachment_name' => $original !== '' ? Str::limit($original, 180, '') : 'file',
            'attachment_size' => $attachment->getSize(),
            'attachment_mime' => (string) $attachment->getClientMimeType(),
        ];
    }

    private function notificationPreview(string $body, bool $hasAttachment): string
    {
        $trimmed = trim($body);

        if ($trimmed !== '') {
            if ($hasAttachment) {
                return __(':message · [File]', ['message' => Str::limit($trimmed, 120)]);
            }

            return Str::limit($trimmed, 120);
        }

        if ($hasAttachment) {
            return __('[File]');
        }

        return '';
    }
}
