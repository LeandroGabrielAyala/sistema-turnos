@php
    $estado = $getState();

    $styles = [
        'confirmado' => '!bg-yellow-100 !text-yellow-800',
        'cancelado' => '!bg-red-100 !text-red-800',
        'atendido' => '!bg-green-100 !text-green-800',
    ];

    $icons = [
        'confirmado' => 'heroicon-o-clock',
        'cancelado' => 'heroicon-o-x-circle',
        'atendido' => 'heroicon-o-check-circle',
    ];
@endphp

<div 
    x-data="{ open: false }" 
    class="relative"
    x-on:click.stop
>

    <!-- BADGE -->
    <span
        @click.stop="open = !open"
        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer border {{ $styles[$estado] ?? '' }}"
    >
        <x-dynamic-component 
            :component="$icons[$estado] ?? 'heroicon-o-question-mark-circle'" 
            class="w-4 h-4"
        />
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
                    <button
                        @click.stop="open = false"
                        wire:click="mountTableAction('atender', {{ $getRecord()->id }})"
                        class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm"
                    >
                        Atendido
                    </button>
                @else
                    <button
                        @click.stop="open = false"
                        wire:click="mountTableAction('cambiarEstado', {{ $getRecord()->id }}, { estado: '{{ $option }}' })"
                        class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm"
                    >
                        {{ ucfirst($option) }}
                    </button>
                @endif

            @endif
        @endforeach
    </div>

</div>