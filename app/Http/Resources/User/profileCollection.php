<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class profileCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'avatar'        => $this->avatar,
            'last_login'    => $this->last_login->diffForHumans(),
            'role'          => $this->role,
            'creted_at'     => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
