@props(['status'])

@php
$class = '';
switch ($status) {
    case 'todo':
        $class = 'bg-slate-200 text-slate-800';
        break;
    case 'in_progress':
        $class = 'bg-blue-200 text-blue-800';
        break;
    case 'done':
        $class = 'bg-emerald-200 text-emerald-800';
        break;
}
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ __(ucwords(str_replace('_', ' ', $status))) }}
</span>
