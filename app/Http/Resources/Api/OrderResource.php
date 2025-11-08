<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment' => $this->whenLoaded('payment', function () {
                return [
                    'id' => $this->payment->id,
                    'method' => $this->payment->method,
                    'status' => $this->payment->status,
                    'amount' => (float) $this->payment->amount,
                    'transaction_id' => $this->payment->transaction_id,
                ];
            }),
            'shipping_address' => [
                'first_name' => $this->shipping_first_name,
                'last_name' => $this->shipping_last_name,
                'phone' => $this->shipping_phone,
                'email' => $this->shipping_email,
                'address' => $this->shipping_address,
                'city' => $this->shipping_city,
                'country' => $this->shipping_country,
                'postal_code' => $this->shipping_postal_code,
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}


