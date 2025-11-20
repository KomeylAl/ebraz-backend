<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DepartmentResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar ? asset('storage/' .      $this->avatar) : null,
            'resume' => $this->whenLoaded('resumeRecord'),
            'phone' => $this->phone,
            'email' => $this->email,
            'national_code' => $this->national_code,
            'medical_number' => $this->medical_number,
            'card_number' => $this->card_number,
            'birth_date' => $this->birth_date,
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'days' => $this->days,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
