<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-slate-500">{{ __('Create') }}</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ __('New project') }}</h2>
        </div>
    </x-slot>

    <div class="card-strong p-6">
        <form method="POST" action="{{ route('projects.store') }}">
            @include('projects.partials.form', ['submitLabel' => __('Create project')])
        </form>
    </div>
</x-app-layout>
