<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get items collection (ensure it's loaded)
        $items = $this->whenLoaded('items') ? $this->items : collect([]);
        
        // Calculate subtotal from items
        $subtotal = $items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        // Get discount amount
        $discount = (float) ($this->discount_amount ?? 0);

        // Calculate total
        $tax = (float) ($this->tax_amount ?? 0);
        $shipping = (float) ($this->shipping_amount ?? 0);
        $total = $subtotal + $tax + $shipping - $discount;

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($items),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => max(0, $total),
            'coupon' => $this->whenLoaded('coupon', function () {
                return [
                    'id' => $this->coupon->id,
                    'code' => $this->coupon->code,
                    'discount_type' => $this->coupon->discount_type,
                    'discount_value' => $this->coupon->discount_value,
                ];
            }),
            'items_count' => $items->sum('quantity'),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}


