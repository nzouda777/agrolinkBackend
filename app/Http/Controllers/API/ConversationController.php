<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        return Conversation::with(['participants.user', 'latestMessage'])
            ->whereHas('participants', function($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->latest('updated_at')
            ->paginate(20);
    }

    public function show($id)
    {
        $conversation = Conversation::with(['participants.user', 'messages.sender'])
            ->findOrFail($id);

        // Check if user is a participant
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'Unauthorized to view this conversation'
            ], 403);
        }

        return $conversation;
    }

    public function create(Request $request)
    {
        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'required|exists:users,id',
            'title' => 'nullable|string|max:255',
        ]);

        // Add the current user to the participants list
        $participants = array_unique(array_merge([$request->user()->id], $request->participants));

        // Check if conversation already exists
        $existingConversation = Conversation::whereHas('participants', function($query) use ($participants) {
            $query->whereIn('user_id', $participants);
        })->first();

        if ($existingConversation) {
            return response()->json([
                'message' => 'Conversation already exists',
                'conversation' => $existingConversation
            ]);
        }

        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'title' => $request->title,
            ]);

            foreach ($participants as $userId) {
                DB::table('conversation_participants')->insert([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Conversation created successfully',
                'conversation' => $conversation
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getMessages($id)
    {
        $conversation = Conversation::findOrFail($id);

        // Check if user is a participant
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'message' => 'Unauthorized to view messages'
            ], 403);
        }

        // Update last read timestamp for the user
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->first();
        $participant->update(['last_read_at' => now()]);

        return $conversation->messages()->with('sender')->latest()->paginate(20);
    }
}
