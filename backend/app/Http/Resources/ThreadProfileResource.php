<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if((!$this->is_bianyuan)||(auth('api')->check())){
            $body = $this->body;
        }else{
            $body = '';
        }
        if ((!$this->is_anonymous)||((auth('api')->check())&&(auth('api')->id()===$this->user_id))){
            $author = new UserBriefResource($this->whenLoaded('author'));
        }else{
            $author = null;
        }
        $tongren = null;
        if($this->tongren){
            $tongren = new TongrenResource($this->tongren);
        }
        $component_index_brief=[];
        if($this->component_index_brief){
            $component_index_brief = PostIndexResource::collection($this->component_index_brief);
        }
        $random_review = null;
        if($this->random_review){
            $random_review = new PostBriefResource($this->random_review);
        }
        $recent_rewards = [];
        if($this->recent_rewards){
            $recent_rewards = RewardResource::collection($this->recent_rewards);
        }
        return [
            'type' => 'thread',
            'id' => (int)$this->id,
            'attributes' => [
                'channel_id' => (int)$this->channel_id,
                'title' => (string)$this->title,
                'brief' => (string)$this->brief,
                'body' => (string)$body,
                'is_anonymous' => (bool)$this->is_anonymous,
                'majia' => (string)$this->majia ?? '匿名咸鱼',
                'created_at' => (string)$this->created_at,
                'edited_at' => (string)$this->edited_at,
                'is_locked' => (bool)$this->is_locked,
                'is_public' => (bool)$this->is_public,
                'is_bianyuan' => (bool)$this->is_bianyuan,
                'no_reply' => (bool)$this->no_reply,
                'use_markdown' => (bool)$this->use_markdown,
                'use_indentation' => (bool)$this->use_indentation,
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
            'component_index_brief' => $component_index_brief,
            'random_review' => $random_review,
            'recent_rewards' => $recent_rewards,
            'tongren' => $tongren,
        ];
    }
}
