<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkRegResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hidden_attributes = [];
        $owner = [];
        if((auth('api')->check())&&(auth('api')->id()===$this->user_id||auth('api')->user()->isAdmin())){
            $hidden_attributes = [
                'registered_at' => (string)$this->registered_at,
                'submitted_at' => (string)$this->submitted_at,
                'finished_at' => (string)$this->finised_at,
                'required_critique_thread_id' => (int)$this->required_critique_thread_id,
                'required_critique_done' => (bool)$this->required_critique_done,
                'summary' => (int)$this->summary,
            ];
            $owner = new UserBriefResource($this->whenLoaded('owner'));
        }
        return [
            'type' => 'homework_registration',
            'id' => (int)$this->id,
            'attributes' => [
                'homework_id' => (int)$this->homework_id,
                'role' => (string)$this->role,
                'majia' => (string)$this->majia,
                'order_id' => (int)$this->order_id,
                'thread_id' => (int)$this->thread_id,
                'title' => (string)$this->title,
                'upvote_count' => (int)$this->upvote_count,
                'given_critique_count' => (int)$this->given_critique_count,
                'received_critique_count' => (int)$this->received_critique_count,
            ],
            'hidden_attributes' => $hidden_attributes,
            'owner' => $owner,
        ];
    }
}
