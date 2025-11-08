<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'total_price' => (float) $this->total_price,
            'status' => $this->status,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'slug' => $this->product->slug,
                    'image' => $this->product->image ? asset('storage/' . $this->product->image) : null,
                ];
            }),
            'digital_card' => $this->whenLoaded('digitalCard', function () {
                return [
                    'id' => $this->digitalCard->id,
                    'card_number' => $this->digitalCard->card_number,
                    'pin' => $this->digitalCard->pin,
                    'expiry_date' => $this->digitalCard->expiry_date?->format('Y-m-d'),
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}


