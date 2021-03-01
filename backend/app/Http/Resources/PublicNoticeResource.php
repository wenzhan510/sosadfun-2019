<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicNoticeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'public_notice',
            'id' => (int)$this->id,
            'attributes' => [
                'user_id' => (int)$this->user_id,
                'title' => (string)$this->title,
                'body' => (string)$this->body,
                'created_at' => (string)$this->created_at,
                'edited_at' => (string)$this->edited_at,
            ],
            'author' => new UserBriefResource($this->whenLoaded('author')),
        ];
    }
}
