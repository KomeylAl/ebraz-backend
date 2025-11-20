<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReferralCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        // فقط دیتا، بدون هیچ متا یا تغییر اضافی
        return ReferralResource::collection($this->collection)->resolve();
    }
}
