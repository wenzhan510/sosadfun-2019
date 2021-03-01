<?php

namespace App\Http\Resources;

use App\Models\Quiz;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuizCollection extends ResourceCollection
{
    protected $include_answer;
    /**
     * QuizCollection constructor.
     * @param mixed $resource
     * @param bool $include_answer include answer in returned resource by default
     */
    public function __construct($resource, bool $include_answer = false)
    {
        parent::__construct($resource);
        $this->include_answer = $include_answer;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function(Quiz $resource) use($request){
            return QuizResource::make($resource,$this->include_answer)->toArray($request);
        })->all();
    }
}
