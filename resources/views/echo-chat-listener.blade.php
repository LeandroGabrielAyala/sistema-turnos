<script>
document.addEventListener('livewire:navigated', () => {

    if (window.chatBadgeLoaded) return;
    window.chatBadgeLoaded = true;

    function updateChatBadge() {
        fetch('/chat/unread-count')
            .then(res => res.text())
            .then(count => {
                count = parseInt(count);

                const chatLink = document.querySelector('a[href*="chat"]');
                if (!chatLink) return;

                let badge = chatLink.querySelector('.fi-badge');

                // Si no hay mensajes → eliminar badge
                if (count <= 0) {
                    if (badge) badge.remove();
                    return;
                }

                // Si no existe badge → crearlo con estilos desde el inicio
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = "fi-badge";

                    // Estilos iniciales para que se vea bien desde la primera carga
                    badge.style.backgroundColor = "#4f46e5";
                    badge.style.color = "white";
                    badge.style.borderRadius = "12px";
                    badge.style.padding = "2px 6px";
                    badge.style.fontSize = "12px";
                    badge.style.fontWeight = "bold";
                    badge.style.marginLeft = "6px";
                    badge.style.display = "inline-block";
                    badge.style.minWidth = "20px";
                    badge.style.textAlign = "center";

                    chatLink.appendChild(badge);
                }

                badge.innerText = count > 9 ? '9+' : count;
            });
    }

    updateChatBadge(); // carga inmediata con estilos
    setInterval(updateChatBadge, 5000); // actualización cada 5s
});
</script>