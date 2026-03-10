<div class="flex flex-col h-[600px] border rounded-lg bg-white dark:bg-gray-900">

    <div 
        id="chat-messages"
        class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-800"
        style="scroll-behavior:smooth; max-height:520px;"
    >

        @foreach($messages as $message)

            @php
                $isMine = $message['sender_id'] == auth()->user()->id;
            @endphp

            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                <div class="max-w-xs px-4 py-2 rounded-lg shadow text-sm
                    {{ $isMine 
                        ? 'bg-primary-600 text-white dark:bg-primary-500' 
                        : 'bg-white text-gray-900 border dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600'
                    }}">

                    <div class="text-xs font-bold mb-1 opacity-70">
                        {{ $message['sender_name'] ?? 'Usuario' }}
                    </div>

                    <div>
                        {{ $message['message'] }}
                    </div>

                    <div class="text-[10px] opacity-60 text-right mt-1">
                        {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <form wire:submit.prevent="sendMessage"
        class="p-3 border-t flex gap-2 bg-white dark:bg-gray-900 dark:border-gray-700">

        <input
            type="text"
            wire:model="message"
            placeholder="Escribe un mensaje..."
            class="flex-1 border rounded px-3 py-2 
                   bg-white text-gray-900
                   dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
        >

        <button
            type="submit"
            class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700"
        >
            Enviar
        </button>

    </form>

</div>


<script>

    function scrollToBottom() {

        const chat = document.getElementById('chat-messages');

        if (!chat) return;

        chat.scrollTop = chat.scrollHeight;

    }

    document.addEventListener('DOMContentLoaded', () => {

        const chat = document.getElementById('chat-messages');

        if (!chat) return;

        scrollToBottom();

        const observer = new MutationObserver(() => {

            scrollToBottom();

        });

        observer.observe(chat, { childList: true });

    });


    document.addEventListener('livewire:navigated', () => {

        if (window.chatListenerLoaded) return;

        window.chatListenerLoaded = true;

        Echo.private('chat.{{ $conversationId }}')
            .listen('.MessageSent', (e) => {

                console.log('Realtime recibido:', e);

                Livewire.dispatch('messageReceived', { event: e });

            });

    });

</script>