<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'position' => $this->position,
            'position_label' => $this->getPositionLabel($this->position),
            'settings' => $this->settings ?? [],
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'is_currently_active' => $this->isCurrentlyActive(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get position label in Arabic
     */
    private function getPositionLabel(string $position): string
    {
        return match($position) {
            'homepage' => 'الصفحة الرئيسية',
            'category' => 'صفحات الفئات',
            'product' => 'صفحات المنتجات',
            'footer' => 'الفوتر',
            default => $position,
        };
    }
}

