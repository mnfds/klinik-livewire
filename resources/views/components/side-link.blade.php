@props(['active'])

@php
    $classes = $active ?? false
        // ? 'btn btn-primary'
        // : 'flex items-center p-2 text-sm font-sm rounded-md groupbtn btn-success'

        ? 'flex items-center p-2 font-semibold text-sm rounded-full group bg-primary text-primary-content shadow transition-all duration-200'
        : 'flex items-center p-2 text-base-content/70 font-medium text-sm rounded-full group hover:bg-primary/50 hover:text-primary-content transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

