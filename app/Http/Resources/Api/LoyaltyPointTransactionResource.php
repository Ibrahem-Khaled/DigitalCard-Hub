<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointTransactionResource extends JsonResource
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
            'type' => $this->type,
            'points' => (int) $this->points,
            'balance_before' => (int) $this->balance_before,
            'balance_after' => (int) $this->balance_after,
            'description' => $this->description,
            'source' => $this->source,
            'source_id' => $this->source_id,
            'processed_at' => $this->processed_at ? $this->processed_at->toIso8601String() : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}

