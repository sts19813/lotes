<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
     public function index()
    {
        $users = User::orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,client',
            'is_admin' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);

        $user->role = $request->role;
        $user->is_admin = $request->is_admin;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Datos actualizados correctamente'
        ]);
    }
}
