<?php

namespace App\Events;
use App\Models\News;

class sendNewsNotificationEvent extends Event
{
    public $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }
}
