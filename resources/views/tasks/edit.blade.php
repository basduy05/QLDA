<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Edit') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('Update task') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-5">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')
            @include('tasks.partials.form', ['submitLabel' => __('Save changes')])
        </form>
    </div>
</x-app-layout>
