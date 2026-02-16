<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Admin') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ __('Users') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-6">
        @if (session('status'))
            <p class="text-sm text-emerald-700 mb-4">{{ session('status') }}</p>
        @endif
        @if ($errors->has('role'))
            <p class="text-sm text-red-600 mb-4">{{ $errors->first('role') }}</p>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left table-head">
                    <tr>
                        <th class="py-3">{{ __('Name') }}</th>
                        <th class="py-3">{{ __('Email') }}</th>
                        <th class="py-3">{{ __('Role') }}</th>
                        <th class="py-3">{{ __('Locale') }}</th>
                        <th class="py-3">{{ __('Manage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr>
                            <td class="py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                            <td class="py-4 text-slate-600">{{ $user->email }}</td>
                            <td class="py-4"><span class="badge bg-slate-100 text-slate-700">{{ __(ucwords($user->role)) }}</span></td>
                            <td class="py-4 text-slate-600">{{ strtoupper($user->locale) }}</td>
                            <td class="py-4">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('patch')
                                    <select name="role" class="rounded-md border border-slate-200 px-2 py-1 text-xs">
                                        <option value="admin" @selected($user->role === 'admin')>{{ __('Admin') }}</option>
                                        <option value="user" @selected($user->role === 'user')>{{ __('User') }}</option>
                                    </select>
                                    <button type="submit" class="text-xs px-3 py-1 rounded-full bg-slate-900 text-white">
                                        {{ __('Save') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
