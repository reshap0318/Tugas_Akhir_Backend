<?php

namespace App\Http\Resources\Topic;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Topic\listCollection as topicCollection;

class ActiveCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'topic_id' => $this->topic->id,
            'name' => $this->topic->name,
            'period_id'=> $this->period->id,
            'period_name' => $this->period->name
        ];
    }
}
