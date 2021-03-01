<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadInfoResource extends JsonResource
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
                'created_at' => (string)$this->created_at,
                'is_locked' => (bool)$this->is_locked,
                'is_public' => (bool)$this->is_public,
                'is_bianyuan' => (bool)$this->is_bianyuan,
                'no_reply' => (bool)$this->no_reply,
                'view_count' => (int)$this->views,
                'reply_count' => (int)$this->reply_count,
                'collection_count' => (int)$this->collections,
                'download_count' => (int)$this->downloads,
                'jifen' => (int)$this->jifen,
                'weighted_jifen' => (int)$this->weighted_jifen,
                'total_char' => (int)$this->total_char,
                'responded_at' => (string)$this->responded_at,
                'add_component_at' => (string)$this->add_component_at,
                'deletion_applied_at' => (string)$this->deletion_applied_at,
            ],
            'author' => $author,
            'tags' => TagInfoResource::collection($this->whenLoaded('tags')),
            'last_component' => new PostBriefResource($this->whenLoaded('last_component')),
            'last_post' => new PostBriefResource($this->whenLoaded('last_post')),
        ];
    }

}
