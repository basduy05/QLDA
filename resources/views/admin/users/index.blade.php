<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Users') }}</h1>
            <a href="{{ route('admin.settings.ai.edit') }}" class="px-3 py-2 text-sm border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                {{ __('AI Settings') }}
            </a>
        </div>
    </x-slot>

    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        @if (session('status'))
            <div class="p-4 text-sm text-emerald-700 bg-emerald-50 border-b border-emerald-200">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('role') || $errors->has('manage'))
            <div class="p-4 text-sm text-rose-700 bg-rose-50 border-b border-rose-200">
                {{ $errors->first('role') ?? $errors->first('manage') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Email') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Role') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">{{ __('Language') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($users as $user)
                        @php($updateFormId = 'user-update-'.$user->id)
                        @php($deleteFormId = 'user-delete-'.$user->id)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <input
                                    type="text"
                                    name="name"
                                    form="{{ $updateFormId }}"
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </td>
                            <td class="px-6 py-4">
                                <input
                                    type="email"
                                    name="email"
                                    form="{{ $updateFormId }}"
                                    value="{{ old('email', $user->email) }}"
                                    class="w-full px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                            </td>
                            <td class="px-6 py-4">
                                <select name="role" form="{{ $updateFormId }}" class="px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="admin" @selected($user->role === 'admin')>{{ __('Admin') }}</option>
                                    <option value="user" @selected($user->role === 'user')>{{ __('User') }}</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <select name="locale" form="{{ $updateFormId }}" class="px-2 py-1 text-sm border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="vi" @selected($user->locale === 'vi')>VI</option>
                                    <option value="en" @selected($user->locale === 'en')>EN</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                <form id="{{ $updateFormId }}" method="POST" action="{{ route('admin.users.update', $user) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                        {{ __('Save') }}
                                    </button>
                                </form>

                                <form id="{{ $deleteFormId }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Delete this user?') }}')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-xs border border-slate-300 text-slate-700 rounded hover:bg-slate-50">
                                        {{ __('Delete') }}
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
