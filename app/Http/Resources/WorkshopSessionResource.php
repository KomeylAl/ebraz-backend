<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        return [
            'id'           => $this->id,
            'session_date' => $this->session_date,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
            'location'     => $this->location,
            'link'         => $this->link,
            'title'        => $this->title,
            'description'  => $this->description,
            'created_at'   => $this->created_at,
        ];
    }
}
