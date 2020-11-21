<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\JsonResource;

class listCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'description' => $this->description,
            'tanggal'   => $this->created_at->format('F d, Y'),
            'waktu'     => $this->created_at->format('g:i A'),
        ];
    }
}
