<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'current_price' => (float) $this->current_price,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'is_in_stock' => $this->is_in_stock,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'images' => $this->images ? array_map(fn($img) => asset('storage/' . $img), $this->images) : [],
            'brand' => $this->brand,
            'card_provider' => $this->card_provider,
            'card_type' => $this->card_type,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'tags' => $this->tags ?? [],
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'reviews' => $this->whenLoaded('reviews', function () {
                return ReviewResource::collection($this->reviews);
            }),
            'average_rating' => $this->when(isset($this->average_rating), $this->average_rating),
            'reviews_count' => $this->when(isset($this->reviews_count), $this->reviews_count),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}


