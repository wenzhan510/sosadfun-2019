<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class VoteResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function toArray($request)
    {
        $author = null;
        $receiver = null;
        $votable = $this->getVotableResource(
            $this->votable_type, $this->whenLoaded('votable'));
        if ($this->showAuthor()){
            $author = new UserBriefResource($this->whenLoaded('author'));
        }
        if ($this->showReceiver()){
            $receiver = new UserBriefResource($this->whenLoaded('receiver'));
        }
        return [
            'type' => 'vote',
            'id' => (int)$this->id,
            'attributes' => [
                'votable_type' => (string)$this->votable_type,
                'votable_id' => (int)$this->votable_id,
                'attitude_type' => (string)$this->attitude_type,
                'created_at' => (string)$this->created_at,
            ],
            'author' => $author,
            'receiver' => $receiver,
            'votable' => $votable,
        ];
    }

    private function getVotableResource($votable_type, $votable){
        switch ($votable_type) {
            case 'post':
                return new PostBriefResource($votable);
            case 'quote':
                return new QuoteResource($votable);
            case 'status':
                // TODO: statusBriefResource?
                return new StatusResource($votable);
            case 'thread':
                return new ThreadBriefResource($votable);
            }
        return null;
    }
    private function isUpvote(){
        return $this->attitude_type === 'upvote';
    }

    private function isVoteForMe(){
        return auth('api')->check()&&auth('api')->id()===$this->user_id;
    }

    private function isOwnVote(){
        return auth('api')->check()&&auth('api')->id()===$this->receiver_id;
    }

    private function isAdmin(){
        return auth('api')->check()&&auth('api')->user()->isAdmin();
    }

    private function showAuthor(){
        return $this->isUpvote()||$this->isOwnVote()||$this->isAdmin();
    }

    private function showReceiver(){
        return $this->isVoteForMe()||$this->isAdmin();
    }

}
