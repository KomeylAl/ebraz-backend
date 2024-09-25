<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function getAllAdmins() {
        $doctors = User::query()->where('role', 'admin')->get();
        return response()->json($doctors, 200);
    }

    public function getAdmin($id) {
        $doctor = User::query()->where('role', 'admin')->where('id', $id)->get();
        return response()->json($doctor, 200);
    }

    public function addAdmin(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required'
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'address.required' => 'فیلد آدرس الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'password.required' => 'فیلد تاریخ تولد الزامی است.',
        ]);

        $client = User::query()->create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request-> phone,
            'birth_date' => $request->birth_date,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);

        return response()->json($client, 201);
    }

    public function editAdmin(Request $request ,$id) {

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'password' => 'required',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($id, 'id')],
            'birth_date' => 'required',
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'address.required' => 'فیلد آدرس الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'password.required' => 'فیلد رمز عبور الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
        ]);

        $client = DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'role' => 'admin'
        ]);

        return response()->json($client, 200);
    }

    public function deleteAdmin($id) {
        User::query()->where('id', $id)->delete();
        return response(['successful'], 200);
    }
}
