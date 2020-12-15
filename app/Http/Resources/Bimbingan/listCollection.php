<?php

namespace App\Http\Resources\Bimbingan;

use Illuminate\Http\Resources\Json\JsonResource;

class listCollection extends JsonResource
{
    public function toArray($request)
    {
        $to = $this->receiver_id;
        $userName = $this->receiver->name;
        $avatar = $this->receiver->getAvatar();
        if($to==app('auth')->user()->id){
            $to = $this->sender_id;
            $userName = $this->sender->name;
            $avatar = $this->sender->getAvatar();
        }
        return [
            'to' => $to,
            'topicPeriodId' => $this->topic_period_id,
            'topic' => $this->topicPeriod->topic->name,
            'period' => $this->topicPeriod->period->name,
            'totalChat' => $this->totalChat,
            'lastChat' => $this->lastChat,
            'namaUser' => $userName,
            'avataUser' => $avatar
        ];
    }
}
