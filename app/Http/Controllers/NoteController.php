<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $note = Note::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'content' => $request->content
        ]);

        return response()->json([
            'message' => 'Note saved',
            'note' => $note
        ], 201);
    }

    public function fetch($id)
    {
        $note = Note::where('user_id', $id)->get();

        if (!$note) {
            return response()->json([
                'message' => 'Could not find notes.'
            ], 404);
        }

        return response()->json([
            'message' => 'Fetched all notes.',
            'notes' => $note
        ], 201);
    }

    public function delete($id)
    {
        $note = Note::find($id);
        $note->delete();

        return response()->json(['message' => 'Note deleted'], 201);
    }
}
