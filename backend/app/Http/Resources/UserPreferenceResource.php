<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
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
                'no_upvote_reminders' => (boolean)$this->no_upvote_reminders,
                'no_reward_reminders' => (boolean)$this->no_reward_reminders,
                'no_message_reminders' => (boolean)$this->no_message_reminders,
                'no_reply_reminders' => (boolean)$this->no_reply_reminders,
                'no_stranger_msg' => (boolean)$this->no_stranger_msg,
                'default_list_id' => (int)$this->default_list_id,
                'default_box_id' => (int)$this->default_box_id,
                'default_collection_group_id' => (int)$this->default_collection_group_id,
            ],
        ];
    }
}
