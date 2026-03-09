<div class="flex flex-col h-[600px] border rounded-lg bg-white">

    <div 
        id="chat-messages"
        class="flex-1 overflow-y-auto p-4 space-y-2 bg-gray-50"
    >

        @foreach($messages as $message)

            @php
                $isMine = $message['sender_id'] == auth()->user()->id;
            @endphp

            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                <div class="max-w-xs px-4 py-2 rounded-lg shadow
                    {{ $isMine ? 'bg-green-500 text-white' : 'bg-white border' }}">

                    <div class="text-xs font-bold mb-1">
                        {{ $message['sender_name'] ?? 'Usuario' }}
                    </div>

                    <div>
                        {{ $message['message'] }}
                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <form wire:submit.prevent="sendMessage"
        class="p-3 border-t flex gap-2 bg-white">

        <input
            type="text"
            wire:model="message"
            placeholder="Escribe un mensaje..."
            class="flex-1 border rounded px-3 py-2"
        >

        <button
            type="submit"
            class="bg-primary-600 text-white px-4 py-2 rounded"
        >
            Enviar
        </button>

    </form>

</div>

<script>
document.addEventListener('livewire:init', () => {

    Echo.private('chat.{{ $conversationId }}')
        .listen('.MessageSent', (e) => {

            Livewire.dispatch('messageReceived', e);

        });

});
</script>