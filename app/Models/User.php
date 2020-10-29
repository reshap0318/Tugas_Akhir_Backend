<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $table = 'users';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'role',
        'avatar',
        'last_login',
        'password',
        'api_token',
        'fcm_token'
    ];

    protected $hidden = [
        'password',
        'api_token',
        'fcm_token'
    ];
}
