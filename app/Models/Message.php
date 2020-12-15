<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{Storage};

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'sender_id',
        'receiver_id',
        'message',
        'read',
        'path_img',
        'time',
        'topic_period_id'
    ];

    protected $dates = [
        'time',
    ];

    public function sender()
    {
        return $this->hasOne(User::class, 'id','sender_id');
    }

    public function receiver()
    {
        return $this->hasOne(User::class, 'id','receiver_id');
    }

    public function topicPeriod()
    {
        return $this->hasOne(PeriodTopic::class, 'id','topic_period_id');
    }

    public function getImg()
    {
        $patlink = rtrim(app()->basePath('public/storage'), '/');
        if($this->path_img && is_dir($patlink) && Storage::disk('public')->exists($this->path_img)){
            return url("/storage/".$this->path_img);
            // return config('app.url')."/storage/".$this->avatar;
        }
        return "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".urlencode("bimbingan_".time());
    }
}
