<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'doctor_id' => $this->doctor_id,
            'title' => $this->title,
            'bio' => $this->bio,
            'specialization' => $this->specialization,
            'educations' => $this->educations,
            'experiences' => $this->experiences,
            'skills' => $this->skills,
            'certifications' => $this->certifications,
            'social_links' => $this->social_links,
            'file' => $this->file ? asset('storage/' . $this->file) : null,
        ];
    }
}
