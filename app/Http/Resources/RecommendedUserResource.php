<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommendedUserResource extends JsonResource
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
            'age' => $this->age,
            'gender' => $this->gender,
            'bio' => $this->bio,
            'location' => $this->location,
            'distance_km' => $this->when(isset($this->distance_km), $this->distance_km),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
