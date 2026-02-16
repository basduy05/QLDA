<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessengerTermsController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->messenger_terms_accepted_at) {
            return redirect()->route('messenger.index');
        }

        return view('messenger.terms');
    }

    public function accept(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->forceFill([
            'messenger_terms_accepted_at' => now(),
        ])->save();

        return redirect()->route('messenger.index')
            ->with('status', __('Terms accepted. You can now use Messenger.'));
    }

    public function decline(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard')
            ->with('status', __('You declined Messenger terms.'));
    }
}
