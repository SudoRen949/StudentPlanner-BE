<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Note;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function delete($id)
    {
        $user = User::find($id)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Could not found user account'
            ], 404);
        }

        $user->delete();

        Note::where('user_id', $id)->delete();
        Subject::where('user_id', $id)->delete();
        Assignment::where('user_id', $id)->delete();
        Schedule::where('user_id', $id)->delete();

        return response()->json([ 'message' => 'Account deleted' ], 201);
    }

    public function reset_password(Request $request)
    {
        $data = $request->validate([
            'id'                => 'required',
            'current_password'  => 'required|string|min:8',
            'password'          => 'required|string|min:8|confirmed'
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'Validation error'
            ], 401);
        }

        $user = User::find($request->id)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Could not find user account'
            ], 404);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Wrong password'
            ], 401);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Successfuly reset password'
        ], 201);
    }
}
