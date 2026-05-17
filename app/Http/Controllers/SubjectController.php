<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'required|string|max:255',
            'title'         => 'required|string|max:255',
            'difficulty'    => 'required|string|max:255',
            'color'         => 'required|string|max:255'
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'Validation failed'
            ], 401);
        }

        $subject = Subject::create([
            'user_id'       => $request->user_id,
            'title'         => $request->title,
            'difficulty'    => $request->difficulty,
            'color'    => $request->color,
        ]);

        return response()->json([
            'message' => 'Subject has been added',
            'subject' => $subject
        ], 201);
    }

    public function fetch($id)
    {
        $subject = Subject::where('user_id', $id)->get();

        if (!$subject) {
            return response()->json([
                'message' => 'No subjects found'
            ], 404);
        }

        return response()->json([
            'message' => 'Subjects found',
            'subject' => $subject
        ], 201);
    }

    public function delete($id)
    {
        $subject = Subject::find($id);
        $subject->delete();

        if (!$subject) {
            return response()->json([
                'message' => 'No subjects found'
            ], 404);
        }

        return response()->json([
            'message' => 'Subjects found'
        ], 201);
    }
}
