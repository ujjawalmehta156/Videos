<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'status' => $this->status,
            'subcategories' => SubCategoryResource::collection($this->whenLoaded('subcategories')),
            'video_count' => $this->when(isset($this->collections_count), $this->collections_count),
        ];
    }
}
