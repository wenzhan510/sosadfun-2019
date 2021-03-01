<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizOptionResource extends JsonResource
{
    protected $include_explanation;

    /**
     * QuizOptionResource constructor.
     * @param mixed $resource
     * @param bool $include_explanation include explanation in returned resource by default
     */
    public function __construct($resource, bool $include_explanation = false)
    {
        parent::__construct($resource);
        $this->include_explanation = $include_explanation;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $is_admin = auth('api')->check() && auth('api')->user() && auth('api')->user()->isAdmin();
        $include_explanation = $this->include_explanation || $is_admin;
        return [
            'type' => 'quiz_option',
            'id' => (int)$this->id,
            'attributes' => [
                'body' => (string)$this->body,
                'explanation' => $this->when($include_explanation,(string)$this->explanation),
                $this->mergeWhen(auth('api')->check() && auth('api')->user() && auth('api')->user()->isAdmin(), [
                    'quiz_id' => (int)$this->quiz_id,
                    'is_correct' => (bool)$this->is_correct,
                    'select_count' => (int)$this->select_count,
                    'edited_at' => (string)$this->edited_at
                ]),
            ]
        ];
    }
}
