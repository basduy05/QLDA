<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500">{{ __('Admin') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('Users') }}</h2>
            </div>
            <a href="{{ route('admin.settings.ai.edit') }}" class="btn-secondary">{{ __('AI Settings') }}</a>
        </div>
    </x-slot>

    <div class="card-strong p-6">

        @if ($errors->has('role'))
            <p class="text-sm text-red-600 mb-4">{{ $errors->first('role') }}</p>
        @endif
        @if ($errors->has('manage'))
            <p class="text-sm text-red-600 mb-4">{{ $errors->first('manage') }}</p>
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
                        @php($updateFormId = 'user-update-'.$user->id)
                        @php($deleteFormId = 'user-delete-'.$user->id)
                        <tr>
                            <td class="py-4">
                                <input
                                    type="text"
                                    name="name"
                                    form="{{ $updateFormId }}"
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full rounded-md border border-slate-200 px-2 py-1 text-xs"
                                    required
                                >
                            </td>
                            <td class="py-4">
                                <input
                                    type="email"
                                    name="email"
                                    form="{{ $updateFormId }}"
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full rounded-md border border-slate-200 px-2 py-1 text-xs"
                                    required
                                >
                            </td>
                            <td class="py-4">
                                <select name="role" form="{{ $updateFormId }}" class="rounded-md border border-slate-200 px-2 py-1 text-xs">
                                    <option value="admin" @selected($user->role === 'admin')>{{ __('Admin') }}</option>
                                    <option value="user" @selected($user->role === 'user')>{{ __('User') }}</option>
                                </select>
                            </td>
                            <td class="py-4">
                                <select name="locale" form="{{ $updateFormId }}" class="rounded-md border border-slate-200 px-2 py-1 text-xs">
                                    <option value="vi" @selected($user->locale === 'vi')>VI</option>
                                    <option value="en" @selected($user->locale === 'en')>EN</option>
                                </select>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-2">
                                    <form id="{{ $updateFormId }}" method="POST" action="{{ route('admin.users.update', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs px-3 py-1 rounded-full bg-slate-900 text-white">{{ __('Save') }}</button>
                                    </form>

                                    <form id="{{ $deleteFormId }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-3 py-1 rounded-full border border-red-200 text-red-600">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
