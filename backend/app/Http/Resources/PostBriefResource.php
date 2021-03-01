<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostBriefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
     //在thread index页面，用于返回最后一个component或最后一个post,最最简略什么都没有
    public function toArray($request)
    {
        return [
            'type' => 'post',
            'id' => (int)$this->id,
            'attributes' => [
                'post_type' => (string) $this->type,
                'thread_id' => (int)$this->thread_id,
                'title' => (string)$this->title,
                'brief' => (string)$this->brief,
                'created_at' => (string)$this->created_at,
                'is_bianyuan' => (bool)$this->is_bianyuan,
            ],
            'thread' => new ThreadBriefResource($this->whenLoaded('simpleThread')),
            'author' => new UserBriefResource($this->whenLoaded('author')),
        ];
    }
}
