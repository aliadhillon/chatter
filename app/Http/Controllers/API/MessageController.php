<?php

namespace App\Http\Controllers\API;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Message as ResourcesMessage;
use App\Http\Resources\MessageCollection;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index() : MessageCollection
    {
        $messages = Message::with('user')->get();

        return new MessageCollection($messages);
    }

    public function store(Request $request) : ResourcesMessage
    {
        $validated = $request->validate([
            'body' => 'required'
        ]);

        $message = Auth::user()->messages()->create($validated);

        $message->load('user');
        
        broadcast(new NewMessage($message))->toOthers();

        return new ResourcesMessage($message);
    }
}
