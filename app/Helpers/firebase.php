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

    public static function unSubscribeAllTopicByDeviceId($deviceId)
    {
        //unsuscribe 
        $messaging = app('firebase.messaging');
        $appInstance = $messaging->getAppInstance($deviceId);
        $subscriptions = $appInstance->topicSubscriptions();
        foreach ($subscriptions as $subscription) {
            if($subscription->registrationToken()==$deviceId){
                $messaging->unsubscribeFromTopic($subscription->topic(), $deviceId);
            }
        }
    }

    public static function unSubscribeAllTopic(string $uid)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId = $database->getReference('Users/'.$uid.'/device-id')->getValue();
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

    public static function sendNotificationToUID(string $uid,array $data, $chat=true)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');

            $newKey = $database->getReference('Users')->push()->getKey();
            $updates = [ 'Users/'.$uid.'/Notification/'.$newKey => $data ];
            if($chat){
                $result = $database->getReference()->update($updates);
            }
            $deviceId =  $database->getReference('Users/'.$uid.'/device-id')->getValue();
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
                    'time' => date("H:i.Y-m-d")
                ]
            ];
            $result = $database->getReference()->update($updates);
            return true;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function sendChatGroup($data)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');

            $newKey = $database->getReference('Chat')->push()->getKey();
            $updates = [ 
                'Group-Chat/'.$data->groupchanel.'/'.$newKey => [
                    'id' => $data->id,
                    'sender' => $data->sender_id,
                    'senderName' => $data->sender->name,
                    'senderAvatar' => $data->sender->getAvatar(),
                    'receiver' => $data->receiver_id,
                    'receiverName' => $data->receiver->name,
                    'topic_period' => 'RSP01',
                    'message' => $data->message,
                    'img' => $data->path_img ? $data->getImg() : "",
                    'time' => date("H:i.Y-m-d")
                ]
            ];
            $result = $database->getReference()->update($updates);
            $notification = Notification::fromArray([
                'title' => "Group Bimbingan ".$data->receiver->name,
                'body' => $data->message,
                'type' => 'chat-group'
            ]);
            $message = CloudMessage::withTarget('topic', $data->groupchanel)->withNotification($notification);
            $messaging->send($message);
            return true;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function deleteMessage($idMessage, $userId)
    {
        $database = app('firebase.database');
        $ref = $database->getReference('Chat');
        $data = $ref->orderByChild('id')->equalTo((int)$idMessage)->getSnapshot()->getValue();
        if($data){
            $set = array_keys($data)[0];
            $ref->update([$set=>null]);
            return true;
        }   
        return false;
        
    }
}