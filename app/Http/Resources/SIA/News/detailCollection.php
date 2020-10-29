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
            'body' => $this->body,
            'user' => '',
            'creted_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
