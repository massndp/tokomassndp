<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //get user
        $users = User::when(request()->q, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(10);

        return new UserResource(true, 'List data user', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        if ($user) {
            return new UserResource(true, 'Data user berhasil ditambahkan', $user);
        }

        return new UserResource(false, 'Data user gagal ditambahkan', null);
    }

    public function show($id)
    {
        $user = User::whereId($id)->first();

        if ($user) {
            return new UserResource(true, 'Detail data user', $user);
        }

        return new UserResource(false, 'Detail data user tidak ditemukan', null);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == "") {
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);
        }

        //update with new password
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if ($user) {
            return new UserResource(true, 'Data user berhasil diupdate', $user);
        }

        return new UserResource(false, 'Data user gagal diupdate', null);
    }

    public function destroy(User $user)
    {
        if ($user->delete()) {
            return new UserResource(true, 'Data user berhasil dihapus', null);
        }

        return new UserResource(false, 'Data user gagal dihapus', null);
    }
}
