<?php

namespace App\Listeners;

use App\Events\sendNewsNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\unit;
use App\Helpers\firebase;
use Illuminate\Support\Facades\Log;

class sendNewsNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  sendNewsNotificationEvent  $event
     * @return void
     */
    public function handle(sendNewsNotificationEvent $event)
    {
        $data = $event->news;
        $mUnitId = $data->unit_id;
        $notice = [
            'title' => $data->title,
            'body' => $data->description,
            'type' => 'news',
            'id' => $data->id
        ];
        $units = unit::all();
        $units = unit::whereRaw("id = $mUnitId or unit_id = $mUnitId")->get();
        foreach($units as $unit){
            firebase::sendNotificationToTopic($unit->id, $notice);
        }
        Log::info("Product was Created, product name");
    }
}
