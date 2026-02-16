<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatGroupController extends Controller
{
    public function index()
    {
        $this->purgeExpiredMessages();

        /** @var User $user */
        $user = Auth::user();

        $groupsQuery = ChatGroup::with(['creator', 'members'])
            ->withCount('messages')
            ->latest();

        if (! $user->isAdmin()) {
            $groupsQuery->whereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }

        return view('chat-groups.index', [
            'groups' => $groupsQuery->paginate(12),
            'users' => User::orderBy('name')->get(),
            'isAdmin' => $user->isAdmin(),
            'authUserId' => $user->id,
        ]);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $group = ChatGroup::create([
            'name' => $data['name'],
            'created_by' => $user->id,
        ]);

        $memberIds = collect($data['member_ids'] ?? [])
            ->push($user->id)
            ->unique()
            ->values()
            ->all();

        $group->members()->sync($memberIds);

        return redirect()->route('messenger.group', $group)
            ->with('status', __('Group created successfully.'));
    }

    public function show(ChatGroup $chatGroup): RedirectResponse
    {
        $this->ensureAccess($chatGroup);

        return redirect()->route('messenger.group', $chatGroup);
    }

    public function messages(ChatGroup $chatGroup)
    {
        $this->purgeExpiredMessages();
        $this->ensureAccess($chatGroup);

        $messages = $chatGroup->messages()
            ->with('user')
            ->latest()
            ->take(150)
            ->get()
            ->reverse()
            ->values();

        return view('chat-groups.partials.messages', [
            'messages' => $messages,
        ]);
    }

    public function updateNickname(Request $request, ChatGroup $chatGroup): RedirectResponse
    {
        $this->ensureAccess($chatGroup);

        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'nickname' => ['nullable', 'string', 'max:40'],
        ]);

        $chatGroup->members()->updateExistingPivot($user->id, [
            'nickname' => trim((string) ($data['nickname'] ?? '')) ?: null,
        ]);

        return back()->with('status', __('Nickname updated.'));
    }

    public function destroy(ChatGroup $chatGroup): RedirectResponse
    {
        $this->ensureManagePermission($chatGroup);
        $chatGroup->delete();

        return redirect()->route('chat-groups.index')
            ->with('status', __('Group deleted.'));
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

    private function ensureManagePermission(ChatGroup $group): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ((int) $group->created_by !== (int) $user->id) {
            abort(403);
        }
    }

    private function purgeExpiredMessages(): void
    {
        ChatMessage::where('created_at', '<', now()->subDay())->delete();
    }
}
