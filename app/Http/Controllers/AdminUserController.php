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
            'role' => ['required', 'in:admin,user'],
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
            'role' => $data['role'],
        ]);

        return back()->with('status', __('User updated successfully.'));
    }
}
