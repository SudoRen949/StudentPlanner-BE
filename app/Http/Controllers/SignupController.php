<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SignupController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Student registered successfully',
            'user' => $user
        ], 201);
    }

    public function update(Request $request)
    {
        // 1. Validation
        $data = Validator::make($request->all(), [
            'email'      => 'required|string|max:255',
            'student_id' => 'required|string|max:50',
            'school'     => 'required|string|max:255',
            'course'     => 'required|string|max:255',
            'year'       => 'required|string|max:20',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }

        // 2. Update the authenticated user
        $user = User::where('email', $request->email)->first();
        $user->student_id = $request->student_id;
        $user->school = $request->school;
        $user->course = $request->course;
        $user->year = $request->year;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);
    }
}