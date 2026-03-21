<div 
    x-data="{ open: false, image: '' }"
    class="flex gap-3 flex-wrap"
>

    @foreach ($getState() as $receta)
        <img 
            src="{{ asset('storage/' . $receta) }}" 
            class="w-32 h-32 object-cover rounded-lg border cursor-pointer hover:scale-105 transition"
            @click="image = '{{ asset('storage/' . $receta) }}'; open = true"
        >
    @endforeach

    <!-- MODAL -->
    <div 
        x-show="open" 
        x-transition
        class="fixed inset-0 bg-black/80 flex items-center justify-center z-50"
        @click.self="open = false"
    >
        <img 
            :src="image" 
            class="max-w-3xl max-h-[90vh] rounded-lg shadow-lg"
        >

        <!-- Botón cerrar -->
        <button 
            class="absolute top-5 right-5 text-white text-3xl"
            @click="open = false"
        >
            ✕
        </button>
    </div>

</div>