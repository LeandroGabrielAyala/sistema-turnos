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

    protected $listeners = ['messageReceived'];

    public function messageReceived($event = null)
    {
        if (!$event) {
            return;
        }

        if ($event['sender_id'] == auth()->id()) {
            return;
        }

        $this->messages[] = $event;
    }

    public function mount($conversationId)
    {
        $this->conversationId = $conversationId;

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

        broadcast(new \App\Events\MessageSent($msg))->toOthers();

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