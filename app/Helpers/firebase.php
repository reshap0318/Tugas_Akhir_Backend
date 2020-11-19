<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class firebase
{

    public static function firebaseCreateUser(array $data)
    {
        try{
            $auth = app('firebase.auth');
            $createdUser = $auth->createUser($data);
            return $createdUser->uid; 
        }
        catch (\Exception $e) {
            return null; 
        }
    }

    public static function saveNotification(string $uid,array $data)
    {
        try{
            $database = app('firebase.database');
            $newKey = $database->getReference('User-Notification')->push()->getKey();
            $updates = [ 'User-Notification/'.$uid.'/Notification/'.$newKey => $data ];
            $result = $database->getReference()->update($updates);
            return $result->getChild('User-Notification/'.$uid.'/device-id')->getValue();
        }
        catch (\Exception $e) {
            return null; 
        }
    }

    public static function updateDeviceId(string $uid, string $device_id)
    {
        try{
            $database = app('firebase.database');
            $updates = [
                'User-Notification/'.$uid.'/device-id' => $device_id
            ];
            $result = $database->getReference()->update($updates);
            return $result->getChild('User-Notification/'.$uid.'/device-id')->getValue();
        }
        catch (\Exception $e) {
            return null; 
        }
    }

    public static function subscribeTopic(string $uid,string $topic)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId =  $database->getReference('User-Notification/'.$uid.'/device-id')->getValue();
            
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

    public static function unsubscribeTopic(string $uid,string $topic)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId =  $database->getReference('User-Notification/'.$uid.'/device-id')->getValue();
            
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

    public static function sendNotificationToUID(string $uid,array $data)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');

            $newKey = $database->getReference('User-Notification')->push()->getKey();
            $updates = [ 'User-Notification/'.$uid.'/Notification/'.$newKey => $data ];
            $result = $database->getReference()->update($updates);
            $deviceId =  $result->getChild('User-Notification/'.$uid.'/device-id')->getValue();
            
            if($deviceId){
                $notification = Notification::fromArray($data);
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

    public static function sendNotificationToTopic(string $topic,array $data)
    {
        try{
            $messaging = app('firebase.messaging');
            $notification = Notification::fromArray($data);
            $message = CloudMessage::withTarget('topic', $topic)->withNotification($notification);
            $messaging->send($message);
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }
}