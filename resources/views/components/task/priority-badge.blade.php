@props(['priority'])

@php
$class = '';
switch ($priority) {
    case 'low':
        $class = 'bg-gray-200 text-gray-800';
        break;
    case 'medium':
        $class = 'bg-yellow-200 text-yellow-800';
        break;
    case 'high':
        $class = 'bg-rose-200 text-rose-800';
        break;
}
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ __(ucwords($priority)) }}
</span>
