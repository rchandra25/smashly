@props(['elo', 'change' => null, 'size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
    ];

    $changeSizeClasses = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
    <span class="font-bold text-gray-900 {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}">
        {{ $elo }}
    </span>
    @if($change !== null && $change !== 0)
        <span class="ml-1 font-medium {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }} {{ $changeSizeClasses[$size] ?? $changeSizeClasses['md'] }}">
            {{ $change >= 0 ? '+' : '' }}{{ $change }}
        </span>
    @endif
</span>
