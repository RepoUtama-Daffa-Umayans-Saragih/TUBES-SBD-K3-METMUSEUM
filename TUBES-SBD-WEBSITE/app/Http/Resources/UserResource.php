<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'premium_started_at' => $this->premium_started_at,
            'premium_ended_at' => $this->premium_ended_at,
            'profile' => new UserProfileResource($this->whenLoaded('profile')),
        ];
    }
}
