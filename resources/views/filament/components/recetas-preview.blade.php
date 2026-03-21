<div class="flex gap-3 flex-wrap">
    @foreach ($getState() as $receta)
        <img 
            src="{{ asset('storage/' . $receta) }}" 
            class="w-32 h-32 object-cover rounded-lg border cursor-pointer hover:scale-105 transition"
            onclick="window.open(this.src, '_blank')"
        >
    @endforeach
</div>