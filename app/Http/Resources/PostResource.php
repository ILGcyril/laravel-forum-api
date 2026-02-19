<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,

            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'topic_id' => $this->topic_id,

            'likes_count' => $this->likes_count,

            'created_at' => $this->created_at,
        ];
    }
}
