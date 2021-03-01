<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkBriefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'homework',
            'id' => (int)$this->id,
            'attributes' => [
                'title' => (string)$this->title,
                'topic' => (string)$this->topic,
                'level' => (int)$this->level,
                'is_active' => (bool)$this->is_active,
                'purchase_count' => (int)$this->purchase_count,
                'worker_count' => (int)$this->worker_count,
                'critic_count' => (int)$this->critic_count,
            ],
        ];
    }
}
