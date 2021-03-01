<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $info = null;
        if($this->type==='chapter'||$this->type==='review'){
            $info = new PostInfoBriefResource($this->whenLoaded('simpleInfo'));
        }
        $parent = null;
        if($this->type==='answer'){
            $parent = new PostResource($this->whenLoaded('parent'));
        }
        return [
            'type' => 'post',
            'id' => (int)$this->id,
            'attributes' => [
                'post_type' => (string) $this->type,
                'thread_id' => (int)$this->thread_id,
                'title' => (string)$this->title,
                'brief' => (string)$this->brief,
                'created_at' => (string)$this->created_at,
                'edited_at' => (string)$this->edited_at,
                'is_bianyuan' => (bool)$this->is_bianyuan,
                'upvote_count' => (int)$this->upvote_count,
                'reply_count' => (int)$this->reply_count,
                'view_count' => (int)$this->views,
                'char_count' => (int)$this->char_count,
                'is_comment' => (boolean)$this->is_comment,
                'len' => (string) $this->len,
            ],
            'info' => $info,
            'parent' => $parent,
            'tags' => TagInfoResource::collection($this->whenLoaded('tags')),
            'thread' => new ThreadBriefResource($this->whenLoaded('simpleThread')),
        ];
    }
}
