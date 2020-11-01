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
        if($this->avatar){
            if(!Storage::disk('public')->exists($this->avatar)){
                return "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".urlencode($this->name);
            }
            return config('app.url')."/storage/".$this->avatar;
        }
        else {
           return "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".urlencode($this->name);
        }
        
    }
}
