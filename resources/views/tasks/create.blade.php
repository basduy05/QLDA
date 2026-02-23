<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('New task') }}</h1>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-lg p-6">
            <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
                @csrf
                @include('tasks.partials.form', ['submitLabel' => __('Create task')])
            </form>
        </div>
    </div>
</x-app-layout>
