<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'is_default'      => $this->is_default,
            'is_active'       => $this->is_active,
            'notify_contact'  => $this->notify_contact,
            'created_at'      => $this->created_at,
        ];
    }
}
