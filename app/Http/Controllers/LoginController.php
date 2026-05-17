<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user
        ], 201);
    }

    public function resession($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'Could not find user account'
            ], 404);
        }

        return response()->json([
            'message' => 'Returned to session',
            'user' => $user
        ], 201);
    }
}