<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">{{ __('Inbox') }}</p>
                <h2 class="text-3xl font-semibold text-slate-900">{{ __('Notifications') }}</h2>
            </div>
            <form method="POST" action="{{ route('notifications.read') }}">
                @csrf
                <button type="submit" class="btn-secondary">{{ __('Mark all read') }}</button>
            </form>
        </div>
    </x-slot>

    <div class="card-strong p-6">
        <div class="space-y-4">
            @forelse ($notifications as $notification)
                <div class="card p-4 {{ $notification->read_at ? '' : 'border-amber-200 bg-amber-50' }}">
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ $notification->data['task_title'] ?? __('Notification') }}</span>
                        <span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-slate-700 mt-2">
                        {{ $notification->data['commenter'] ?? '' }}
                        {{ $notification->data['body'] ?? '' }}
                    </p>
                    @if (!empty($notification->data['task_id']))
                        <a class="text-sm text-slate-600" href="{{ route('tasks.show', $notification->data['task_id']) }}">{{ __('View task') }}</a>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500">{{ __('No notifications yet.') }}</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
