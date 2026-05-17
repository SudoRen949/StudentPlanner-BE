<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|string|max:255',
            'title'      => 'required|string|max:255',
            'difficulty' => 'required|string|max:255',
            'deadline'   => 'required|string|max:255',
            'completed'  => 'required|boolean'
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'Validation error'
            ], 401);
        }

        $assignment = Assignment::create([
            'user_id'    => $request->user_id,
            'title'      => $request->title,
            'difficulty' => $request->difficulty,
            'deadline'   => $request->deadline,
            'completed'  => $request->completed
        ]);

        return response()->json([
            'message' => 'Assignment added',
            'assignment' => $assignment
        ], 201);
    }

    public function fetch($id)
    {
        $assignment = Assignment::where('user_id', $id)->get();

        if (!$assignment) {
            return response()->json([
                'message' => 'No assignments found'
            ], 404); 
        }

        return response()->json([
            'message'    => 'Assignments fetched',
            'assignment' => $assignment
        ], 201);
    }

    public function delete($id)
    {
        $assignment = Assignment::find($id)->first();
        $assignment->delete();

        return response()->json([
            'message'    => 'Assignments deleted'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'completed' => 'required|boolean'
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'Validation error'
            ], 401);
        }

        $assignment = Assignment::find($id);
        $assignment->completed = $request->completed;
        $assignment->save();

        return response()->json([
            'message'    => 'Assignments updated',
            'assignment' => $assignment
        ], 201);
    }
}
