<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function editUser(Request $request ,$id) {

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->ignore($id, 'id')],
        ],[
            'name.required' => 'فیلد نام الزامی است.',
            'address.required' => 'فیلد آدرس الزامی است.',
            'phone.required' => 'فیلد تلفن الزامی است.',
            'phone.max' => 'تلفن نمی تواند بیشتر از 15 کاراکتر باشد.',
            'phone.unique' => 'این شماره تلفن قبلا ثبت شده است.',
        ]);

        $client = DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'role' => $request->role
        ]);

        return response()->json($client, 200);
    }

    public function deleteAdmin($id) {
        User::query()->where('id', $id)->delete();
        return response(['successful'], 200);
    }
}
