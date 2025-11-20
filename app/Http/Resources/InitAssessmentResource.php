<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InitAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $client = $this->clients->first();
        $doctor = $this->doctors->first();

        return [
            'id' => $this->id,
            'doctor' => $doctor,
            'client' => $client,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
