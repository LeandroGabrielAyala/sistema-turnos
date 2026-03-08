<!DOCTYPE html>
<html>
<head>
    <title>Chat Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="p-10">

<h1 class="text-2xl mb-4">Chat de prueba</h1>

<livewire:chat-box :conversationId="1" />

@livewireScripts
</body>
</html>