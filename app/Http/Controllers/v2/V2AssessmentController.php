<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InitAssessment;
use App\Models\Client;
use App\Models\Doctor;
use App\Http\Resources\InitAssessmentResource;

class V2AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InitAssessment::with(['client', 'doctor']);
        $doctor = Auth()->user();

        $doctor_id = $doctor->id;
        $query->whereHas('doctor', function ($q) use ($doctor_id) {
            $q->where('doctors.id', $doctor_id);
        });

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $perPage = (int) $request->query('per_page', 10);
        $assessments = $query->paginate($perPage);

        return InitAssessmentResource::collection($assessments);
    }
}
