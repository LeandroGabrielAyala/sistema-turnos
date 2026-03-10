<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatBox extends Component
{
    public $conversationId;
    public $message = '';
    public $messages = [];
    public $typingUser = null;
    public $isTyping = false;

    protected $listeners = [
        'messageReceived',
        'userTyping'
    ];

    public function userTyping($user)
    {
        if ($user == auth()->user()->name) {
            return;
        }

        $this->typingUser = $user;

        $this->isTyping = true;

        $this->dispatch('clearTyping');
    }

    public function typing()
    {
        broadcast(
            new \App\Events\UserTyping(
                $this->conversationId,
                auth()->user()->name
            )
        )->toOthers();
    }

    public function messageReceived($event = null)
    {
        if (!$event) {
            return;
        }

        if ($event['sender_id'] == auth()->id()) {
            return;
        }

        // marcar como leído
        \App\Models\ChatMessage::where('id', $event['id'])
            ->update([
                'read_at' => now()
            ]);

        $this->messages[] = $event;

        $this->dispatch('refresh-navigation');
    }

    public function mount($conversationId)
    {
        $this->conversationId = $conversationId;

        ChatMessage::where('conversation_id', $conversationId)
            ->whereNull('read_at')
            ->where('sender_id', '!=', auth()->id())
            ->update([
                'read_at' => now()
            ]);

        $this->messages = ChatMessage::with('sender')
            ->where('conversation_id', $conversationId)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'created_at' => $msg->created_at,
                ];
            })
            ->toArray();
    }

    public function sendMessage()
    {
        $msg = ChatMessage::create([
            'conversation_id' => $this->conversationId,
            'sender_id' => auth()->id(),
            'message' => $this->message,
        ]);

        broadcast(new MessageSent($msg));

        // AGREGAR MENSAJE LOCALMENTE
        $this->messages[] = [
            'id' => $msg->id,
            'message' => $msg->message,
            'sender_id' => $msg->sender_id,
            'sender_name' => auth()->user()->name,
            'created_at' => $msg->created_at,
        ];

        $this->message = '';
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}