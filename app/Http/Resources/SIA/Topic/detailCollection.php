<?php

namespace App\Http\Resources\SIA\Topic;

use Illuminate\Http\Resources\Json\JsonResource;

class detailCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'creted_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
