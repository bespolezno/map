<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if ($user = User::where(['username' => $request->username])->first()
            and Hash::check($request->password, $user->password)) {
            return response()->json([
                'token' => $user->generateToken(),
                'role' => $user->is_admin ? 'ADMIN' : 'USER'
            ], 200);
        }

        return response()->json(['message' => 'invalid login'], 401);
    }

    public function logout()
    {
        Auth::user()->clearToken();

        return response()->json(['message' => 'logout success'], 200);
    }
}
