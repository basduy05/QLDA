<?php

namespace App\Http\Controllers;

use App\Models\CallSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallSessionController extends Controller
{
    private const RING_TIMEOUT_SECONDS = 45;

    public function start(User $contact)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($contact->id === $user->id) {
            return response()->json(['message' => 'Invalid callee.'], 422);
        }

        $existing = CallSession::query()
            ->where(function ($query) use ($user, $contact) {
                $query->where('caller_id', $user->id)->where('callee_id', $contact->id);
            })
            ->orWhere(function ($query) use ($user, $contact) {
                $query->where('caller_id', $contact->id)->where('callee_id', $user->id);
            })
            ->whereIn('status', ['ringing', 'active'])
            ->latest()
            ->first();

        if ($existing && $existing->status === 'ringing' && optional($existing->created_at)?->lt(now()->subSeconds(self::RING_TIMEOUT_SECONDS))) {
            $existing->update([
                'status' => 'missed',
                'ended_at' => now(),
            ]);
            $existing = null;
        }

        if ($existing) {
            return response()->json(['call' => $this->serialize($existing)], 200);
        }

        $call = CallSession::create([
            'caller_id' => $user->id,
            'callee_id' => $contact->id,
            'status' => 'ringing',
        ]);

        return response()->json(['call' => $this->serialize($call)], 201);
    }

    public function poll()
    {
        /** @var User $user */
        $user = Auth::user();

        CallSession::query()
            ->where(function ($query) use ($user) {
                $query->where('caller_id', $user->id)
                    ->orWhere('callee_id', $user->id);
            })
            ->where('status', 'ringing')
            ->where('created_at', '<', now()->subSeconds(self::RING_TIMEOUT_SECONDS))
            ->update([
                'status' => 'missed',
                'ended_at' => now(),
            ]);

        $baseQuery = CallSession::query()
            ->where(function ($query) use ($user) {
                $query->where('caller_id', $user->id)
                    ->orWhere('callee_id', $user->id);
            });

        $call = (clone $baseQuery)
            ->whereIn('status', ['ringing', 'active'])
            ->latest('updated_at')
            ->first();

        if (! $call) {
            $call = (clone $baseQuery)
                ->whereIn('status', ['ended', 'rejected', 'missed'])
                ->where('updated_at', '>=', now()->subSeconds(15))
                ->latest('updated_at')
                ->first();
        }

        return response()->json([
            'call' => $call ? $this->serialize($call) : null,
        ]);
    }

    public function show(CallSession $callSession)
    {
        $this->ensureAccess($callSession);

        return response()->json(['call' => $this->serialize($callSession)]);
    }

    public function accept(CallSession $callSession)
    {
        /** @var User $user */
        $user = Auth::user();
        $this->ensureAccess($callSession);

        if ($callSession->callee_id !== $user->id) {
            return response()->json(['message' => 'Only callee can accept.'], 403);
        }

        if ($callSession->status === 'ringing') {
            $callSession->update([
                'status' => 'active',
                'accepted_at' => now(),
            ]);
        }

        return response()->json(['call' => $this->serialize($callSession->fresh())]);
    }

    public function reject(CallSession $callSession)
    {
        $this->ensureAccess($callSession);

        if (in_array($callSession->status, ['ringing', 'active'], true)) {
            $callSession->update([
                'status' => 'rejected',
                'ended_at' => now(),
            ]);
        }

        return response()->json(['call' => $this->serialize($callSession->fresh())]);
    }

    public function end(CallSession $callSession)
    {
        $this->ensureAccess($callSession);

        if (in_array($callSession->status, ['ringing', 'active'], true)) {
            $callSession->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
        }

        return response()->json(['call' => $this->serialize($callSession->fresh())]);
    }

    public function signal(Request $request, CallSession $callSession)
    {
        $this->ensureAccess($callSession);

        if (! in_array($callSession->status, ['ringing', 'active'], true)) {
            return response()->json(['message' => 'Call is no longer active.'], 422);
        }

        $data = $request->validate([
            'type' => ['required', 'in:offer,answer'],
            'sdp' => ['required', 'string'],
        ]);

        if ($data['type'] === 'offer') {
            $callSession->update(['offer_sdp' => $data['sdp']]);
        }

        if ($data['type'] === 'answer') {
            $callSession->update(['answer_sdp' => $data['sdp']]);
        }

        return response()->json(['call' => $this->serialize($callSession->fresh())]);
    }

    private function ensureAccess(CallSession $callSession): void
    {
        $userId = (int) Auth::id();

        if (! $callSession->involves($userId)) {
            abort(403);
        }
    }

    private function serialize(CallSession $callSession): array
    {
        return [
            'id' => $callSession->id,
            'caller_id' => $callSession->caller_id,
            'callee_id' => $callSession->callee_id,
            'status' => $callSession->status,
            'offer_sdp' => $callSession->offer_sdp,
            'answer_sdp' => $callSession->answer_sdp,
            'accepted_at' => optional($callSession->accepted_at)?->toISOString(),
            'ended_at' => optional($callSession->ended_at)?->toISOString(),
            'updated_at' => optional($callSession->updated_at)?->toISOString(),
        ];
    }
}
