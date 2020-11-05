<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\Storage;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $table = 'users';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'username',
        'avatar',
        'last_login',
        'role',
        'unit_id',
        'password',
        'api_token',
        'fcm_token'
    ];

    protected $hidden = [
        'password',
        'api_token',
        'fcm_token'
    ];

    protected $dates = [
        'last_login',
    ];

    public function FileNameAvatar()
    {
        $id = $this->id ? $this->id : "not-found";
        return uniqid("avatar-user-".$id."-");
    }

    public function getAvatar()
    {
        
        $patlink = rtrim(app()->basePath('public/storage'), '/');
        if($this->avatar && is_dir($patlink) && Storage::disk('public')->exists($this->avatar)){
            return config('app.url')."/storage/".$this->avatar;
        }
        return "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".urlencode($this->name);
        
    }
}
