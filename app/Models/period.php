<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'periods';

    protected $fillable = [
        'id',
        'name'
    ];

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'period_topics', 'topic_id', 'period_id');
    }
}
