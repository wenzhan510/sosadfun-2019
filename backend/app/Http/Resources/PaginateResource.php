<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
    public function __construct($resource)
    {
        $this->pagination = [
            'total' => (int)$resource->total(),
            'count' => (int)$resource->count(),
            'per_page' => (int)$resource->perPage(),
            'current_page' => (int)$resource->currentPage(),
            'total_pages' => (int)$resource->lastPage()
        ];
    }
    public function toArray($request)
    {
        return $this->pagination;
    }
}
