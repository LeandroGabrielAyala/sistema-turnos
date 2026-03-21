@php
    $styles = [
        'confirmado' => 'bg-warning-100 text-warning-700',
        'cancelado' => 'bg-danger-100 text-danger-700',
        'atendido' => 'bg-success-100 text-success-700',
    ];
@endphp

<div x-data="{ open: false }" class="relative" @click.stop>

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
        @if ($option !== $estado)

            @if ($option === 'atendido')
                <!-- ABRE MODAL -->
                <button
                    wire:click="mountTableAction('atender', {{ $getRecord()->id }})"
                    class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm"
                >
                    Atendido
                </button>
            @else
                <!-- CAMBIO DIRECTO -->
                <button
                    wire:click="callTableAction('cambiarEstado', {{ $getRecord()->id }}, '{{ $option }}')"
                    class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm"
                >
                    {{ ucfirst($option) }}
                </button>
            @endif

        @endif
    @endforeach
    </div>

</div>