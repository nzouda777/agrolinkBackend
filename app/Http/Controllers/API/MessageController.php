<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:1000',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        // Check if user is a participant
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'Unauthorized to send message'
            ], 403);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        // Update conversation timestamps
        $conversation->touch();

        return response()->json([
            'message' => 'Message sent successfully',
            'message' => $message
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to update message'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message->update($request->all());

        return response()->json([
            'message' => 'Message updated successfully',
            'message' => $message
        ]);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        if ($message->sender_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to delete message'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }
}
