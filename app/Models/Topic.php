<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    protected $table = 'topics';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::random(5);
        });
    }
}
