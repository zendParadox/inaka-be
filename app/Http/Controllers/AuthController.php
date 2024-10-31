<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // dd($user);
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['user' => $user, 'accessToken' => $token]);
            // return response()->json(['user' => $user]);
            // echo "$user";
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}
