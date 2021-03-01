<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
        $author = null;
        if((!$this->is_anonymous)||((auth('api')->check())&&(auth('api')->id()===$this->user_id||auth('api')->user()->isAdmin()))){
            $author = new UserBriefResource($this->whenLoaded('author'));
        }
        $info = [];
        if($this->type==='chapter'||$this->type==='review'){
            $info = new PostInfoFullResource($this->whenLoaded('info'));
        }
        $parent = [];
        if($this->type==='answer'){
            $parent = new PostResource($this->whenLoaded('parent'));
        }
        $last_reply = new PostBriefResource($this->whenLoaded('last_reply'));
        $recent_rewards = [];
        if($this->recent_rewards){
            $recent_rewards = RewardResource::collection($this->recent_rewards);
        }
        $recent_upvotes = [];
        if($this->recent_upvotes){
            $recent_upvotes = VoteResource::collection($this->recent_upvotes);
        }
        $new_replies = [];
        if($this->new_replies){
            $new_replies = PostResource::collection($this->new_replies);
        }
        return [
            'type' => 'post',
            'id' => (int)$this->id,
            'attributes' => [
                'post_type' => (string) $this->type,
                'thread_id' => (int)$this->thread_id,
                'title' => (string)$this->title,
                'brief' => (string)$this->brief,
                'body' => (string)$body,
                'is_anonymous' => (bool)$this->is_anonymous,
                'majia' => (string)$this->majia,
                'created_at' => (string)$this->created_at,
                'edited_at' => (string)$this->edited_at,
                'in_component_id' => (int) $this->in_component_id,
                'reply_to_id' => (int)$this->reply_to_id,
                'reply_to_brief' => (string)$this->reply_to_brief,
                'reply_to_position' => (int)$this->reply_to_position,
                'fold_state' => (int) $this->fold_state,
                'is_bianyuan' => (bool)$this->is_bianyuan,
                'use_markdown' => (bool)$this->markdown,
                'use_indentation' => (bool)$this->use_indentation,
                'upvote_count' => (int)$this->upvote_count,
                'downvote_count' => (int)$this->downvote_count,
                'funnyvote_count' => (int)$this->funnyvote_count,
                'foldvote_count' => (int)$this->foldvote_count,
                'reply_count' => (int)$this->reply_count,
                'view_count' => (int)$this->views,
                'char_count' => (int)$this->char_count,
                'responded_at' => (string)$this->responded_at,
                'is_comment' => (boolean)$this->is_comment,
                'len' => (string) $this->len,
            ],
            'author' => $author,
            'info' => $info,
            'parent' => $parent,
            'last_reply' => $last_reply,
            'tags' => TagInfoResource::collection($this->whenLoaded('tags')),
            'thread' => new ThreadBriefResource($this->whenLoaded('simpleThread')),
            'recent_rewards' => $recent_rewards,
            'recent_upvotes' => $recent_upvotes,
            'new_replies' => $new_replies,
        ];
    }
}
