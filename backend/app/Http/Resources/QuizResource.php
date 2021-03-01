<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    protected $include_answer;

    /**
     * QuizResource constructor.
     * @param mixed $resource
     * @param bool $include_answer include answer in returned resource by default
     */
    public function __construct($resource, bool $include_answer = false)
    {
        parent::__construct($resource);
        $this->include_answer = $include_answer;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $quiz_type = '';
        if ($this->type == "register") {
            $quiz_type = 'quiz';
        } elseif ($this->type == "essay") {
            $quiz_type = 'essay';
        } elseif ($this->type == "level_up") {
            $quiz_type = 'quiz';
        }
        $is_admin = auth('api')->check() && auth('api')->user() && auth('api')->user()->isAdmin();
        $include_answer = in_array($this->type, config('constants.quiz_has_option')) && ($this->include_answer || $is_admin);
        $quiz_options = collect($this->quiz_options) ?? $this->whenLoaded('quiz_options');
        $correct_answer = [];
        $options = [];
        if (in_array($this->type, config('constants.quiz_has_option'))) {
            $correct_answer = $quiz_options->where('is_correct',true)->pluck('id')->toArray();
            $options = new QuizOptionCollection($quiz_options,$include_answer);
        }
        return [
            'type' => $quiz_type,
            'id' => (int)$this->id,
            'attributes' => [
                'body' => (string)$this->body,
                'hint' => (string)$this->hint,
                'correct_answer' => $this->when($include_answer,$correct_answer),
                $this->mergeWhen(auth('api')->check() && auth('api')->user() && auth('api')->user()->isAdmin(), [
                    'type' => (string)$this->type,
                    'is_online' => (bool)$this->is_online,
                    'level' => (int)$this->quiz_level,
                    'quiz_count' => (int)$this->quiz_count,
                    'correct_count' => (int)$this->correct_count,
                    'edited_at' => (string)$this->edited_at
                ]),
                'options' => $this->when(in_array($this->type, config('constants.quiz_has_option')), $options)
            ]
        ];
    }
}
