<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'unit_price' => (float) $this->unit_price,
            'current_stock' => $this->current_stock,
            'is_active' => $this->is_active,
            'category' => [
                'id' => $this->whenLoaded('category', fn () => $this->category->id),
                'name' => $this->whenLoaded('category', fn () => $this->category->name),
            ],
        ];
    }
}
