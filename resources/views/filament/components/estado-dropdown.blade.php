@php
    $estado = $getState();

    $styles = [
        'confirmado' => 'bg-blue-100 text-blue-800',
        'cancelado' => 'bg-red-100 text-red-800',
        'atendido' => 'bg-green-100 text-green-800',
    ];

    $icons = [
        'confirmado' => 'heroicon-o-clock',
        'cancelado' => 'heroicon-o-x-circle',
        'atendido' => 'heroicon-o-check-circle',
    ];
@endphp

<div x-data="{ open: false }" class="relative">

    <!-- BADGE -->
    <span
        @click="open = !open"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer {{ $styles[$estado] ?? 'bg-gray-100' }}"
    >
        <x-dynamic-component :component="$icons[$estado] ?? 'heroicon-o-question-mark-circle'" class="w-4 h-4"/>
        {{ ucfirst($estado) }}
    </span>

    <!-- DROPDOWN -->
    <div 
        x-show="open"
        x-transition
        @click.away="open = false"
        class="absolute mt-2 w-44 bg-white border rounded-lg shadow-lg z-50"
    >
        @foreach (['confirmado', 'cancelado', 'atendido'] as $option)
            <button
                wire:click="callTableAction('cambiarEstado', {{ $getRecord()->id }}, '{{ $option }}')"
                class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm"
            >
                {{ ucfirst($option) }}
            </button>
        @endforeach
    </div>

</div>