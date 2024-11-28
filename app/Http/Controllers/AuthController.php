<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'recaptcha' => 'required',
        ]);

        // Verifikasi token reCAPTCHA
        $client = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => env('RECAPTCHA_SECRET'),
                'response' => $request->input('recaptcha'),
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);

        if (!$body['success'] || $body['score'] < 0.5) {
            return response()->json(['message' => 'reCAPTCHA gagal diverifikasi.'], 400);
        }

        // Ambil hanya email dan password untuk Auth::attempt()
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['user' => $user, 'accessToken' => $token]);
        } else {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }
    }
}
