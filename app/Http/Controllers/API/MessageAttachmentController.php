<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MessageAttachmentController extends Controller
{
    public function store(Request $request, $messageId)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:5120', // 5MB max
            'type' => 'required|string',
        ]);

        $message = Message::findOrFail($messageId);
        
        // Vérifier si l'utilisateur peut ajouter des pièces jointes
        if ($message->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // Stocker le fichier
        $path = $file->storeAs('messages', $filename);

        $message->attachments()->create([
            'path' => $path,
            'type' => $validated['type'],
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);

        return response()->noContent();
    }

    public function destroy($messageId, $attachmentId)
    {
        $attachment = MessageAttachment::where('message_id', $messageId)
            ->findOrFail($attachmentId);

        // Vérifier si l'utilisateur peut supprimer la pièce jointe
        if ($attachment->message->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Supprimer le fichier du stockage
        Storage::delete($attachment->path);
        
        $attachment->delete();
        return response()->noContent();
    }

    public function download($messageId, $attachmentId)
    {
        $attachment = MessageAttachment::where('message_id', $messageId)
            ->findOrFail($attachmentId);

        // Vérifier si l'utilisateur peut accéder au fichier
        if ($attachment->message->user_id !== auth()->id() && 
            !$attachment->message->conversation->participants()->where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return Storage::download($attachment->path, $attachment->original_name);
    }
}
