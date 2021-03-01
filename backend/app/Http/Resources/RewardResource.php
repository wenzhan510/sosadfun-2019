<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
        // TODO double check if "whenloaded" is properly working when not loaded.
        $rewardable = $this->getRewardableResource(
            $this->rewardable_type, $this->whenLoaded('rewardable'));
        $author = new UserBriefResource($this->whenLoaded('author'));
        if($this->showReceiver()){
            $receiver = new UserBriefResource($this->whenLoaded('receiver'));
        }

        return [
            'type' => 'reward',
            'id' => (int)$this->id,
            'attributes' => [
                'rewardable_type' => (string)$this->rewardable_type,
                'rewardable_id' => (int)$this->rewardable_id,
                'reward_type' => (string)$this->reward_type,
                'reward_value' => (int)$this->reward_value,
                'created_at' => (string)$this->created_at,
                'deleted_at' => (string)$this->deleted_at,
            ],
            'author' => $author,
            'receiver' => $receiver,
            'rewardable' => $rewardable,
        ];
    }

    private function getRewardableResource($rewardable_type, $rewardable){
        switch ($rewardable_type) {
            case 'post':
                return new PostBriefResource($rewardable);
            case 'quote':
                return new QuoteResource($rewardable);
            case 'status':
                // TODO: statusBriefResource?
                return new StatusResource($rewardable);
            case 'thread':
                return new ThreadBriefResource($rewardable);
            }
        return null;
    }
    private function isOwnReward(){
        return auth('api')->check()&&auth('api')->id()===$this->user_id;
    }
    private function isRewardForMe(){
        return auth('api')->check()&&auth('api')->id()===$this->receiver_id;
    }
    private function isAdmin(){
        return auth('api')->check()&&auth('api')->user()->isAdmin();
    }
    private function showReceiver(){
        return $this->isRewardForMe()||$this->isAdmin();
    }
}
