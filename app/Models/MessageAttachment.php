<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_path',
        'mime_type',
        'original_name'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
