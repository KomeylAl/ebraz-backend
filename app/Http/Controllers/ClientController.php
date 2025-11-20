<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Resources\ClientResource;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        // جستجو
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فیلتر بر اساس تاریخ تولد (اختیاری)
        if ($request->filled('birth_date')) {
            $query->whereDate('birth_date', $request->query('birth_date'));
        }

        // مرتب سازی
        $sortBy = $request->query('sort_by', 'created_at'); // پیشفرض بر اساس created_at
        $sortDirection = $request->query('sort_direction', 'desc'); // پیشفرض نزولی
        $query->orderBy($sortBy, $sortDirection);

        // صفحه‌بندی
        $perPage = (int) $request->query('per_page', 10);
        $clients = $query->paginate($perPage);

        // ریترن به فرمت Resource
        return ClientResource::collection($clients);
    }

    public function show($id)
    {
        $client = Client::with('record')->findOrFail($id);
        return new ClientResource($client);
    }

    public function addClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:clients',
        ], [
            'name.required' => 'فیلد نام الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
        ]);

        $client = Client::query()->create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
        ]);

        return response()->json($client, 201);
    }

    public function editClient(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('clients', 'phone')->ignore($id, 'id')
            ],
        ], [
            'name.required' => 'فیلد نام الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
        ]);

        $client = Client::query()->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
        ]);

        return response()->json($client, 200);
    }

    public function deleteClient($id)
    {
        Client::query()->where('id', $id)->delete();
        return response(['successful'], 200);
    }
}
