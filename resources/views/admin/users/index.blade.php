<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Admin') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ __('Users') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left table-head">
                    <tr>
                        <th class="py-3">{{ __('Name') }}</th>
                        <th class="py-3">{{ __('Email') }}</th>
                        <th class="py-3">{{ __('Role') }}</th>
                        <th class="py-3">{{ __('Locale') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr>
                            <td class="py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                            <td class="py-4 text-slate-600">{{ $user->email }}</td>
                            <td class="py-4"><span class="badge bg-slate-100 text-slate-700">{{ __(ucwords($user->role)) }}</span></td>
                            <td class="py-4 text-slate-600">{{ strtoupper($user->locale) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
