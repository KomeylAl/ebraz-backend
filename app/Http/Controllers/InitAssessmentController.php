<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use Illuminate\Http\Request;
use App\Models\InitAssessment;
use App\Models\Client;
use App\Http\Resources\InitAssessmentResource;

class InitAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InitAssessment::with(['doctor', 'client']);

        if ($request->filled('client_id')) {
            $client_id = $request->query('client_id');
            $query->whereHas('clients', function ($q) use ($client_id) {
                $q->where('clients.id', $client_id);
            });
        }

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            // اطلاعات ارزیابی
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'status' => 'required|in:pending,done',
            // 'file' => 'nullable|file',

            // اطلاعات دکتر
            'doctor_id' => 'nullable|exists:doctors,id',

            // اطلاعات کلاینت (ممکن است جدید باشد)
            'client.phone' => 'required|string',
            'client.name' => 'required|string',
            'client.birth_date' => 'nullable|date',
            'client.address' => 'nullable|string',
        ]);

        // بررسی وجود کلاینت
        $existingClient = Client::where('phone', $validated['client']['phone'])
            ->first();

        if ($existingClient) {
            $client = $existingClient;
        } else {
            // ساخت کلاینت جدید
            $client = Client::create([
                'name' => $validated['client']['name'],
                'phone' => $validated['client']['phone'],
                'birth_date' => $validated['client']['birth_date'] ?? null,
                'address' => $validated['client']['address'] ?? null,
            ]);
        }

        // // ذخیره فایل ارزیابی
        // $filePath = null;
        // if ($request->hasFile('file')) {
        //     $filePath = $request->file('file')->store('assessments', 'public');
        // }

        // ساخت ارزیابی
        $assessment = InitAssessment::create([
            'date' => $validated['date'] ?? null,
            'time' => $validated['time'] ?? null,
            'status' => $validated['status'],
        ]);

        // اتصال به pivot table
        $assessment->client()->attach($client->id, [
            'doctor_id' => $validated['doctor_id'] ?? null
        ]);

        // $notification = \App\Models\Notification::create([
        //     'title' => 'ثبت ارزیابی',
        //     'message' => 'یک نوبت ارزیابی جدید ثبت شد.',
        //     'type' => 'appointment',
        //     'priority' => 'low',
        //     'notifiable_type' => \App\Models\Admin::class,
        //     'notifiable_id' => null,
        // ]);

        // event(new NotificationCreated($notification));

        return response()->json([
            'message' => 'ارزیابی با موفقیت ثبت شد',
            'data' => $assessment->load(['client', 'doctor'])
        ], 201);
    }


    public function destroy(InitAssessment $assessment)
    {
        $assessment->delete();
        return response()->noContent();
    }
}
