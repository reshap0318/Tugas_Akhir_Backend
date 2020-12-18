<?php

namespace App\Helpers;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class firebase
{
    public static function updateDeviceId(string $uid, string $device_id)
    {
        try{
            $database = app('firebase.database');
            $updates = [
                'Users/'.$uid.'/device-id' => $device_id
            ];
            $result = $database->getReference()->update($updates);
            return $result->getChild('Users/'.$uid.'/device-id')->getValue();
        }
        catch (\Exception $e) {
            return null; 
        }
    }

    public static function subscribeTopic(string $topic,string $deviceId)
    {
        try{
            $messaging = app('firebase.messaging');
            if(!$topic){
                $topic = "Unand";
            }

            //unsuscribe 
            $appInstance = $messaging->getAppInstance($deviceId);
            $subscriptions = $appInstance->topicSubscriptions();
            foreach ($subscriptions as $subscription) {
                if($subscription->registrationToken()==$deviceId){
                    $messaging->unsubscribeFromTopic($subscription->topic(), $deviceId);
                }
            }
            if($deviceId){
                $messaging->subscribeToTopic($topic, $deviceId);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function unSubscribeTopic(string $topic,string $uid)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId = $database->getReference('Users/'.$uid.'/device-id')->getValue();
            if($deviceId){
                $messaging->unsubscribeFromTopic($topic, $deviceId);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function unSubscribeAllTopic(string $uid)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId = $database->getReference('Users/'.$uid.'/device-id')->getValue();
            return $deviceId;
            if($deviceId){
                //unsuscribe 
                $appInstance = $messaging->getAppInstance($deviceId);
                $subscriptions = $appInstance->topicSubscriptions();
                foreach ($subscriptions as $subscription) {
                    if($subscription->registrationToken()==$deviceId){
                        $messaging->unsubscribeFromTopic($subscription->topic(), $deviceId);
                    }
                }
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function sendNotificationToUID(string $uid,array $data)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');

            $newKey = $database->getReference('Users')->push()->getKey();
            $updates = [ 'Users/'.$uid.'/Notification/'.$newKey => $data ];
            $result = $database->getReference()->update($updates);
            $deviceId =  $result->getChild('Users/'.$uid.'/device-id')->getValue();
            if($deviceId){
                $notification = Notification::fromArray($data);
                $message = CloudMessage::withTarget('token', $deviceId)->withNotification($notification)->withData($data);
                $messaging->send($message);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function sendNotificationToTopic(string $topic,array $data)
    {
        try{
            $messaging = app('firebase.messaging');
            if(!$topic){
                $topic = "Unand";
            }
            $notification = Notification::fromArray($data);
            $message = CloudMessage::withTarget('topic', $topic)->withNotification($notification);
            $messaging->send($message);
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function sendChat($data)
    {
        try{
            $receiverUid = $data->receiver->fcm_token;
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');

            $newKey = $database->getReference('Chat')->push()->getKey();
            $updates = [ 
                'Chat/'.$newKey => [
                    'id' => $data->id,
                    'sender' => $data->sender_id,
                    'receiver' => $data->receiver_id,
                    'topic_period' => $data->topic_period_id,
                    'message' => $data->message,
                    'isRead' => 0,
                    'img' => $data->path_img ? $data->getImg() : "",
                    'time' => $data->time
                ]
            ];
            $result = $database->getReference()->update($updates);
            $deviceId =  $result->getChild('Users/'.$receiverUid.'/device-id')->getValue();
            if($deviceId){
                $notification = Notification::fromArray([
                    'title' => $data->sender->name,
                    'body' => $data->message,
                    'type' => 'chat'
                ]);
                $message = CloudMessage::withTarget('token', $deviceId)->withNotification($notification);
                $messaging->send($message);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function sendChatGroup($data)
    {
        try{
            $receiverUid = $data->receiver->fcm_token;
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');

            $newKey = $database->getReference('Chat')->push()->getKey();
            $updates = [ 
                'Chat/'.$newKey => [
                    'id' => $data->id,
                    'sender' => $data->sender_id,
                    'receiver' => $data->receiver_id,
                    'topic_period' => 'RSP01',
                    'message' => $data->message,
                    'isRead' => 0,
                    'img' => $data->path_img ? $data->getImg() : "",
                    'time' => $data->time
                ]
            ];
            $result = $database->getReference()->update($updates);
            $deviceId =  $result->getChild('Users/'.$receiverUid.'/device-id')->getValue();
            if($deviceId){
                $notification = Notification::fromArray([
                    'title' => "Group Bimbingan ".$data->receiver->name,
                    'body' => $data->message,
                    'type' => 'chat'
                ]);
                $message = CloudMessage::withTarget('topic', $data->receiver->username)->withNotification($notification);
                $messaging->send($message);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }
}