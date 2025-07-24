<?php

namespace Database\Factories;

use App\Models\MessageAttachment;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageAttachmentFactory extends Factory
{
    protected $model = MessageAttachment::class;

    public function definition(): array
    {
        return [
            'message_id' => Message::factory(),
            'file_path' => $this->faker->filePath(),
            'mime_type' => $this->faker->mimeType(),
            'original_name' => $this->faker->fileName(),
        ];
    }
}
