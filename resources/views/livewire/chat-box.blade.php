<div class="flex flex-col h-[500px] border rounded-lg">

    <div class="flex-1 overflow-y-auto p-4 space-y-2" id="messages">

        @foreach($messages as $msg)

            <div class="p-2 bg-gray-100 rounded-lg">
                {{ $msg['message'] }}
            </div>

        @endforeach

    </div>

    <div class="border-t p-2 flex gap-2">

        <input
            type="text"
            wire:model="message"
            wire:keydown.enter="sendMessage"
            class="flex-1 border rounded px-2 py-1"
            placeholder="Escribe un mensaje..."
        >

        <button
            wire:click="sendMessage"
            class="bg-blue-500 text-white px-3 py-1 rounded"
        >
            Enviar
        </button>

    </div>

</div>

<script>

    Echo.private('chat.{{ $conversationId }}')
    .listen('MessageSent', (e) => {

        Livewire.dispatch('messageReceived', e);

    });

</script>