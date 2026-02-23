<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('Edit project') }}</h1>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-lg p-6">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @method('PUT')
                @include('projects.partials.form', ['submitLabel' => __('Save changes')])
            </form>
        </div>
    </div>
</x-app-layout>
