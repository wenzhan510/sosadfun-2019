<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkResource extends JsonResource
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
                'ham_base' => (int)$this->ham_base,
                'is_active' => (bool)$this->is_active,
                'allow_watch' => (bool)$this->allow_watch,
                'registration_on' => (string)$this->registration_on,
                'worker_registration_limit' => (int)$this->worker_registration_limit,
                'critic_registration_limit' => (int)$this->critic_registration_limit,
                'created_at' => (string)$this->created_at,
                'end_at' => (string)$this->end_at,
                'registration_thread_id' => (int)$this->registration_thread_id,
                'profile_thread_id' => (int)$this->profile_thread_id,
                'summary_thread_id' => (int)$this->summary_thread_id,
                'purchase_count' => (int)$this->purchase_count,
                'worker_count' => (int)$this->worker_count,
                'critic_count' => (int)$this->critic_count,
                'finished_work_count' => (int)$this->finished_work_count,
            ],
            'homework_registrations' => [], //TODO
        ];
    }
}
