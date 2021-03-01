<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $cooldown = (bool)($this->submitted_at > Carbon::now()->subDays(config('constants.application_cooldown_days')));
        return [
            'id' => $this->id,
            'type' => 'registration_application',
            'attributes' => [
                'email' => (string)$this->email,
                'has_quizzed' => (bool)$this->has_quizzed,
                'email_verified_at' => (string)$this->email_verified_at,
                'submitted_at' => (string)$this->submitted_at,
                'is_passed' => (bool)$this->is_passed,
                'last_invited_at' => (string)$this->last_invited_at,
                'is_in_cooldown' => (bool)$cooldown,
                $this->mergeWhen(auth('api')->check() && auth('api')->user() && auth('api')->user()->isAdmin(), [
                    'is_forbidden' => (bool)$this->is_forbidden,
                    'reviewer_id' => (int)$this->reviewer_id,
                    'cut_in_line' => (bool)$this->cut_in_line,
                    'ip_address' => (string)$this->ip_address,
                    'ip_address_last_quiz' => (string)$this->ip_address_last_quiz,
                    'ip_address_verify_email' => (string)$this->ip_address_verify_email,
                    'ip_address_submit_essay' => (string)$this->ip_address_submit_essay,
                    'created_at' => (string)$this->created_at,
                    'reviewed_at' => (string)$this->reviewed_at,
                    'submission_count' => (int)$this->submission_count,
                    'quiz_count' => (int) $this->quiz_count
                ]),
            ]
        ];
    }
}
