<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PeriodTopic extends Model
{
    protected $table = 'period_topics';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'period_id',
        'topic_id'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::random(5);
        });
    }

    public function topic()
    {
        return $this->hasOne(Topic::class, 'id', 'topic_id');
    }

    public function period()
    {
        return $this->hasOne(Period::class, 'id', 'period_id');
    }
}
