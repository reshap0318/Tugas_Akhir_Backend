<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class period extends Model
{
    protected $table = 'periods';

    protected $fillable = [
        'id',
        'slug',
        'name',
        'unit_id'
    ];
}
