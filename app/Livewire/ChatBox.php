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

    public function messageReceived($event)
    {
        if ($event['sender_id'] == auth()->id()) {
            return;
        }

        $this->messages[] = $event;
    }

    public function mount($conversationId)
    {
        $this->conversationId = $conversationId;

        $this->messages = ChatMessage::where('conversation_id', $conversationId)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->toArray();
    }

    public function sendMessage()
    {
        $msg = ChatMessage::create([
            'conversation_id' => $this->conversationId,
            'sender_id' => Auth::id() ?? 1,
            'message' => $this->message
        ]);

        broadcast(new MessageSent($msg))->toOthers();

        $this->messages[] = $msg;

        $this->message = '';
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}