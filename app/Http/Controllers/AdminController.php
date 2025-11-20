<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Resources\AdminResource;

class AdminController extends Controller
{
    public function index(Request $request) {
        $query = Admin::query();

        // جستجو
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function($q) use ($search) {
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
        $admins = $query->paginate($perPage);

        // ریترن به فرمت Resource
        return AdminResource::collection($admins);
    }

    public function getAllRecAdmins() {
        $admins = Admin::where('role', '=', 'receptionist')->get();
        return response()->json($admins, 200);
    }

    public function getAdmin($id) {
        $admin = Admin::query()->where('id', $id)->first(); 
        return response()->json($admin, 200);
    }

    public function addAdmin(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'birth_date' => 'required|string',
            'phone' => 'required|string|max:15|unique:admins',
            'password' => 'required|min:8'
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'role.required' => 'فیلد سمت الزامی است.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'password.required' => 'فیلد رمز عبور الزامی است.',
            'password.min' => 'رمز عبور نمی تواند کمتر از 8 کاراکتر باشد.',
        ]);

        $admin = Admin::query()->create([
            'name' => $request->name,
            'role' => $request->role,
            'phone' => $request-> phone,
            'birth_date' => $request->birth_date,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($admin, 201);
    }

    public function editAdmin(Request $request ,$id) {

        $admin = Admin::where('id', $id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($id, 'id')],
            'birth_date' => 'required',
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'role.required' => 'فیلد سمت الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'password.required' => 'فیلد رمز عبور الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
        ]);

        $edited_admin = Admin::where('id', $id)->update([
            'name' => $request->name,
            'role' => $request->role,
            'phone' => $request-> phone,
            'birth_date' => $request->birth_date,
        ]);

        if ($request->password) {
            Admin::where('id', $id)->update([
                'password' => Hash::make($request->password),
            ]);   
        }

        return response()->json($edited_admin, 200);
    }

    public function destroy($id) {
        Admin::query()->where('id', $id)->delete();
        return response(['successful'], 200);
    }
}
