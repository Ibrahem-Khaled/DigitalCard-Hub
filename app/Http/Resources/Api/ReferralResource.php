<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'referral_code' => $this->referral_code,
            'status' => $this->status,
            'commission_amount' => (float) $this->commission_amount,
            'commission_percentage' => (float) $this->commission_percentage,
            'reward_amount' => (float) $this->reward_amount,
            'reward_percentage' => (float) $this->reward_percentage,
            'completed_at' => $this->completed_at ? $this->completed_at->toIso8601String() : null,
            'expires_at' => $this->expires_at ? $this->expires_at->toIso8601String() : null,
            'referred' => $this->whenLoaded('referred', function () {
                return [
                    'id' => $this->referred->id,
                    'name' => $this->referred->full_name,
                    'email' => $this->referred->email,
                    'avatar' => $this->referred->avatar ? asset('storage/' . $this->referred->avatar) : null,
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}

