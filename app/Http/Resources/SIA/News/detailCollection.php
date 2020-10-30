<?php

namespace App\Http\Resources\SIA\News;

use Illuminate\Http\Resources\Json\JsonResource;

class detailCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user' => '',
            'creted_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
