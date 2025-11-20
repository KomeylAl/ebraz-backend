<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'name_en'          => $this->name_en,
            'national_code'    => $this->national_code,
            'gender'           => $this->gender,
            'phone'            => $this->phone,
            'approved'         => $this->pivot ? $this->pivot->approved : null,
            'created_at'       => $this->created_at,
        ];
    }
}
