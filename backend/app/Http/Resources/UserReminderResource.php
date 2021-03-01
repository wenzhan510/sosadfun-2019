<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReminderResource extends JsonResource
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
            'type' => 'user_info',
            'id' => (int)$this->user_id,
            'attributes' => [
                'unread_reminders' => (int)$this->unread_reminders,
                'unread_updates' => (int)$this->unread_updates,
                'message_reminders' => (int)$this->message_reminders,
                'reply_reminders' => (int)$this->reply_reminders,
                'upvote_reminders' => (int)$this->upvote_reminders,
                'reward_reminders' => (int)$this->reward_reminders,
                'administration_reminders' => (int)$this->administration_reminders,
                'default_collection_updates' => (int)$this->default_collection_updates,
                'public_notice_id' => (int)$this->public_notice_id,
            ],
        ];
    }
}
