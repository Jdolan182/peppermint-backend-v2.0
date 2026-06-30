<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->content,
            'published_at' => $this->published_at,
            'created_at'   => $this->created_at,
            'author'       => $this->whenLoaded('author', fn () => [
                'id'   => $this->author->id,
                'name' => $this->author->name,
            ]),
            'categories'   => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
