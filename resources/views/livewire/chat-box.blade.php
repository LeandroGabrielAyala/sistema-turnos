<div class="max-w-xl mx-auto border rounded p-4">

    <div class="h-80 overflow-y-auto mb-4 border p-2">
        @foreach($messages as $message)
            <div class="mb-2">
                <strong>{{ $message['sender_name'] ?? 'Usuario' }}:</strong>
                {{ $message['message'] ?? $message['content'] }}
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" class="flex gap-2">
        <input 
            type="text" 
            wire:model="message" 
            class="border p-2 flex-1 rounded"
            placeholder="Escribe un mensaje..."
        >

        <button 
            type="submit" 
            class="bg-blue-500 text-white px-4 py-2 rounded"
        >
            Enviar
        </button>
    </form>

</div>

<script>
document.addEventListener('livewire:init', () => {

    Echo.private('chat.{{ $conversationId }}')
        .listen('.MessageSent', (e) => {

            console.log('Nuevo mensaje recibido:', e);

            Livewire.dispatch('messageReceived', { event: e });

        });

});
</script>