document.addEventListener('DOMContentLoaded', () => {

    if (window.chatGlobalListenerLoaded) return;
    window.chatGlobalListenerLoaded = true;

    Echo.private('chat.*')
        .listen('.MessageSent', (e) => {

            console.log('Mensaje global recibido', e);

            window.dispatchEvent(new Event('chat-message-received'));

        });

});