@props(['active'])

@php
    $classes = $active ?? false
        ? 'flex items-center p-2 font-semibold text-sm rounded-md group bg-base-300 text-base-content shadow transition-all duration-200'
        : 'flex items-center p-2 text-base-content/70 font-medium text-sm rounded-md group hover:bg-base-200 hover:text-base-content transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

