<?php

namespace App\Http\Controllers;

use App\Models\DirectConversation;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DirectMessageController extends Controller
{
    public function index()
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        $contacts = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('messages.index', [
            'contacts' => $contacts,
            'activeContact' => null,
            'messages' => collect(),
            'conversation' => null,
            'conversationMap' => $this->buildConversationMap($user),
            'callUrl' => null,
        ]);
    }

    public function show(User $contact)
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            return redirect()->route('messages.index');
        }

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);

        $messages = $conversation->messages()
            ->with('user')
            ->latest()
            ->take(200)
            ->get()
            ->reverse()
            ->values();

        return view('messages.index', [
            'contacts' => User::where('id', '!=', $user->id)->orderBy('name')->get(),
            'activeContact' => $contact,
            'messages' => $messages,
            'conversation' => $conversation,
            'conversationMap' => $this->buildConversationMap($user),
            'callUrl' => $this->callUrl($conversation),
        ]);
    }

    public function feed(User $contact)
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            abort(403);
        }

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);

        $messages = $conversation->messages()
            ->with('user')
            ->latest()
            ->take(200)
            ->get()
            ->reverse()
            ->values();

        return view('messages.partials.feed', [
            'messages' => $messages,
            'authUserId' => $user->id,
        ]);
    }

    public function store(Request $request, User $contact): RedirectResponse
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

        $conversation = $this->findOrCreateConversation($user->id, $contact->id);

        DirectMessage::create([
            'direct_conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => trim($data['body']),
        ]);

        return redirect()->route('messages.show', $contact)
            ->with('status', __('Message sent.'));
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

    private function buildConversationMap(User $authUser): Collection
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

            $lastMessage = $conversation->messages->first();

            $map->put($otherUserId, [
                'conversation_id' => $conversation->id,
                'last_message' => $lastMessage?->body,
                'last_at' => $lastMessage?->created_at,
            ]);
        }

        return $map;
    }

    private function callUrl(DirectConversation $conversation): string
    {
        $room = 'qhorizon-dm-'.$conversation->id;

        return 'https://meet.jit.si/'.$room;
    }

    private function purgeExpiredMessages(): void
    {
        DirectMessage::where('created_at', '<', now()->subDay())->delete();
    }
}
