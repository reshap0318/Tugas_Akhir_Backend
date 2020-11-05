<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class unit extends Model
{
    protected $table = 'units';

    protected $fillable = [
        'id',
        'name',
        'unit_id'
    ];
}
