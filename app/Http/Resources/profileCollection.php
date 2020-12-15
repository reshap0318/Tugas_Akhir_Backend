<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class profileCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'username'      => $this->username,
            'email'         => $this->email,
            'avatar'        => $this->getAvatar(),
            'last_login'    => $this->last_login ? $this->last_login->diffForHumans() : null,
            'role'          => $this->role,
            'creted_at'     => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
