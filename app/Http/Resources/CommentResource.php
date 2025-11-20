<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        return [
            'id'         => $this->id,
            'body'       => $this->body,
            'approved'   => $this->approved,
            'created_at' => $this->created_at->toDateTimeString(),
            'author'     => new UserResource($this->whenLoaded('user')),
        ];
    }
}
