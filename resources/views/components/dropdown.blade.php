@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'start-0',
    'top' => 'origin-top',
    default => 'end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<details class="relative">
    <summary class="dropdown-summary">
        {{ $trigger }}
    </summary>

    <div class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</details>
