<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Create') }}</p>
            <h2 class="text-2xl font-semibold text-slate-900">{{ __('New task') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-5">
        <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
            @csrf
            @include('tasks.partials.form', ['submitLabel' => __('Create task')])
        </form>
    </div>
</x-app-layout>
