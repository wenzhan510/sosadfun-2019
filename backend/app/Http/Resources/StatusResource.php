<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $attachable = null;
        if($this->attachable_type==='post'){
            $attachable = new PostBriefResource($this->whenLoaded('attachable'));
        }
        if($this->attachable_type==='thread'){
            $attachable = new ThreadBriefResource($this->whenLoaded('attachable'));
        }
        if($this->attachable_type==='status'){
            $attachable = new StatusResource($this->whenLoaded('attachable'));
        }

        $recent_rewards = [];
        if($this->recent_rewards){
            $recent_rewards = RewardResource::collection($this->recent_rewards);
        }
        $recent_upvotes = [];
        if($this->recent_upvotes){
            $recent_upvotes = VoteResource::collection($this->recent_upvotes);
        }
        return [
            'type' => 'Status',
            'id' => (int)$this->id,
            'attributes' => [
                'brief' => (string)$this->brief,
                'body' => (string)$this->body,
                'created_at' => (string)$this->created_at,
                'no_reply' => (bool)$this->no_reply,
                'is_public' => (bool)$this->is_public,
                'reply_count' => (int)$this->reply_count,
                'forward_count' => (int)$this->forward_count,
                'upvote_count' => (int)$this->upvote_count,
                'created_at' => (string)$this->created_at,
                'deleted_at' => (string)$this->deleted_at,
                'attacbable_type' => (string)$this->attachable_type,
                'attacbable_id' => (int)$this->attachable_id,
                'reply_to_id' => (int)$this->reply_to_id,
                'last_reply_id' => (int)$this->last_reply_id,
            ],
            'author' => new UserBriefResource($this->whenLoaded('author')),
            'attachable' => $attachable,
            'parent' => new StatusResource($this->whenLoaded('parent')),
            'last_reply' => new StatusResource($this->whenLoaded('last_reply')),
            'replies' => StatusResource::collection($this->whenLoaded('replies')),
            'recent_rewards' => $recent_rewards,
            'recent_upvotes' => $recent_upvotes,
        ];
    }
}
