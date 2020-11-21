<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\JsonResource;

class detailCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'unit' => $this->unit ? $this->unit->name : "Universitas Andalas",
            'creted_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
