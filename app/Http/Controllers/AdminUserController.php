<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,user'],
            'locale' => ['required', 'in:vi,en'],
        ]);

        if ($user->id === $request->user()->id
            && $data['role'] !== 'admin'
            && User::where('role', 'admin')->count() === 1
        ) {
            return back()->withErrors([
                'role' => __('You must keep at least one admin account.'),
            ]);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'locale' => $data['locale'],
        ]);

        return back()->with('status', __('User updated successfully.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = $request->user();

        if ($user->id === $authUser->id) {
            return back()->withErrors([
                'manage' => __('You cannot delete your own account from admin panel.'),
            ]);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return back()->withErrors([
                'manage' => __('You must keep at least one admin account.'),
            ]);
        }

        $user->delete();

        return back()->with('status', __('User deleted successfully.'));
    }
}
