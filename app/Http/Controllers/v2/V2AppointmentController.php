<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Http\Resources\ReferralCollection;
use App\Http\Resources\ReferralResource;

class V2AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Referral::with(['client', 'doctor']);
        $doctor = Auth()->user();

        $doctor_id = $doctor->id;
        $query->whereHas('doctor', function ($q) use ($doctor_id) {
            $q->where('doctors.id', $doctor_id);
        });

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['id', 'created_at', 'amount', 'status'];
        $sortBy = $request->query('sort_by', 'created_at');
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $sortDirection = strtolower($request->query('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDirection);

        $perPage = max(1, min((int) $request->query('per_page', 10), 100));
        $referrals = $query->paginate($perPage);

        return ReferralResource::collection($referrals);
    }
}
