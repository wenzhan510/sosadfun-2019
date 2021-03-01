<?php

namespace App\Http\Resources;

use App\Models\QuizOption;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuizOptionCollection extends ResourceCollection
{
    protected $include_explanation;
    /**
     * QuizCollection constructor.
     * @param mixed $resource
     * @param bool $include_explanation include explanation in returned resource by default
     */
    public function __construct($resource, bool $include_explanation = false)
    {
        parent::__construct($resource);
        $this->include_explanation = $include_explanation;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function(QuizOption $quizOption) use($request){
            return QuizOptionResource::make($quizOption,$this->include_explanation)->toArray($request);
        })->all();
    }
}
