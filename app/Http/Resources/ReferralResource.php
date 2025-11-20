<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    public function toArray($request): array
    {
        $client = $this->client->first();
        $doctor = $this->doctor->first();

        return [
            'referral_id' => $this->id,
            'doctor' => $doctor,
            'client' => $client,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_status' => $this->payment?->status ?? 'unknown',
            'payment' => $this->payment?->amount ?? 0,
        ];
    }
}
