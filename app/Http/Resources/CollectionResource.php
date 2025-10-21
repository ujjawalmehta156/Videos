<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'duration' => $this->duration,
            'views' => $this->views,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubCategoryResource($this->whenLoaded('subcategory')),
            'meta' => new MetaResource($this->whenLoaded('meta')),
            'hls_streams' => $this->whenLoaded('videoStreams', function () {
                return $this->videoStreams->map(function ($stream) {
                    return [
                        'quality' => $stream->quality ?? 'auto',
                        'url' => $stream->hls_url ?? $stream->url,
                        'resolution' => $stream->resolution,
                        'bitrate' => $stream->bitrate,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
