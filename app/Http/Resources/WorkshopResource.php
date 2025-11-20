<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WorkshopSessionResource;
use App\Http\Resources\WorkshopParticipantResource;

class WorkshopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'organizers'  => $this->organizers,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'week_day' => $this->week_day,
            'time' => $this->time,
            'img_path' => $this->img_path,
            'created_at' => $this->created_at,
            'sessions'    => WorkshopSessionResource::collection(
                $this->whenLoaded('sessions')
            ),
            'participants' => WorkshopParticipantResource::collection(
                $this->whenLoaded('participants')
            ),
        ];
    }

}
