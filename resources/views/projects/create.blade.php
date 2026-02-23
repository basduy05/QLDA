<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('New project') }}</h1>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-lg p-6">
            <form method="POST" action="{{ route('projects.store') }}">
                @include('projects.partials.form', ['submitLabel' => __('Create project')])
            </form>
        </div>
    </div>
</x-app-layout>
