<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadBriefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!$this->is_anonymous){
            $author = new UserBriefResource($this->whenLoaded('author'));
        }else{
            $author = null;
        }
        return [
            'type' => 'thread',
            'id' => (int)$this->id,
            'attributes' => [
                'channel_id' => (int)$this->channel_id,
                'title' => (string)$this->title,
                'brief' => (string)$this->brief,
                'is_anonymous' => (bool)$this->is_anonymous,
                'majia' => (string)$this->majia,
                'is_locked' => (bool)$this->is_locked,
                'is_bianyuan' => (bool)$this->is_bianyuan,
                'is_public' => (bool)$this->is_public,
                'no_reply' => (bool)$this->no_reply,
                'deletion_applied_at' => (string)$this->deletion_applied_at,
            ],
            'author' => $author,
            'tags' => TagInfoResource::collection($this->whenLoaded('tags')),
        ];
    }
}
