<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Referral;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PaymentResource;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['referral.client', 'referral.doctor']);

        // فیلتر بر اساس client_id
        if ($request->filled('client_id')) {
            $client_id = $request->query('client_id');
            $query->whereHas('referral.client', function ($q) use ($client_id) {
                $q->where('clients.id', $client_id);
            });
        }

        // جستجو بر اساس نام یا تلفن
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->whereHas('referral.client', function ($q) use ($search) {
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
        $payments = $query->paginate($perPage);

        return PaymentResource::collection($payments);
    }


}
