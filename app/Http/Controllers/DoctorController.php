<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function getAllDoctors() {
        $doctors = User::query()->where('role', 'doctor')->get();
        return response()->json($doctors, 200);
    }

    public function getDoctor($id) {
        $doctor = User::query()->where('role', 'doctor')->where('id', $id)->get();
        return response()->json($doctor, 200);
    }

    public function addDoctor(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:users',
            'birth_date' => 'required',
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
        ]);

        $client = User::query()->create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request-> phone,
            'birth_date' => $request->birth_date,
            'role' => 'doctor'
        ]);

        return response()->json($client, 201);
    }

    public function editDoctor(Request $request ,$id) {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($id, 'id')],
            'birth_date' => 'required',
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
            'birth_date.required' => 'فیلد تاریخ تولد الزامی است.',
        ]);

        $doctor = DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'role' => 'doctor'
        ]);

        return response()->json($doctor, 200);
    }

    public function deleteDoctor($id) {
        User::query()->where('id', $id)->delete();
        return response(['successful'], 200);
    }
}
