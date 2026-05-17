<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI;

class ScheduleController extends Controller
{
    public function generate($id)
    {
        $subject = Subject::where('user_id', $id)->get();
        $assignment = Assignment::where('user_id', $id)->where('completed', false)->get();

        if ($subject->isEmpty() && $assignment->isEmpty()) {
            return response()->json([
                'message' => 'Please add some subjects or assignment first'
            ], 400);
        }

        $context = "Subjects data:\n";
        foreach ($subject as $sub) {
            $context .= "- {$sub->title} (Difficulty: {$sub->difficulty})\n";
        }

        $context .= "\nPending assignments:\n";
        foreach ($assignment as $task) {
            $context .= "- {$task->title} (Difficulty: {$task->difficulty})\n";
        }

        $apiKey = env('OPENAI_API_KEY');
        $baseUrl = env('OPENAI_BASE_URL');

        $guzzleClient = new \GuzzleHttp\Client([ 'verify' => false ]);
        $client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUrl)
            ->withHttpClient($guzzleClient)
            ->make();

        $prompt = "You are an expert student AI study planner. Based on the following data, generate a realistic study schedule for today. Prioritize harder subjects and upcoming assignments. Mix in logical 'Break Time' slots to prevent burnout.

        DATA:
        {$context}

        CRITICAL: Return your response ONLY as a valid JSON array matching this exact schema:
        [
            {
                \"title\": \"Subject or Task name\",
                \"difficulty\": \"Hard|Medium|Easy\",
                \"date\": \"e.g., 02/02/2025\",
                \"time\": \"e.g., 02:00 PM - 03:30 PM\"
            }
        ]
        ";

        try {
            $response = $client->chat()->create([
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [ 'role' => 'system', 'content' => 'You only output minified JSON arrays. No markdown formatting, no text wrapper.' ],
                    [ 'role' => 'user', 'content' => $prompt ]
                ],
                'temperature' => 0.7
            ]);

            $rawJSON = $response->choices[0]->message->content;
            $cleanedJSON = preg_replace('/^```json|```$/', '', trim($rawJSON));
            $decodedData = json_decode($cleanedJSON, true);

            if (isset($decodedData['tasks'])) {
                $scheduleData = $decodedData['tasks'];
            } elseif (isset($decodedData['schedule'])) {
                $scheduleData = $decodedData['schedule'];
            } elseif (isset($decodedData['schedules'])) {
                $scheduleData = $decodedData['schedules'];
            } elseif (is_array($decodedData) && !empty($decodedData) && !isset($decodedData[0])) {
                // If it returned a single object dictionary instead of a sequential array, wrap it
                $scheduleData = [$decodedData];
            } else {
                $scheduleData = $decodedData;
            }

            if (is_array($scheduleData)) {
                foreach ($scheduleData as $rawItem) {
                    // 1. Force all keys in this specific item to lowercase
                    // This transforms "Title", "TITLE", or "tItLe" into "title"
                    $item = array_change_key_case($rawItem, CASE_LOWER);

                    // 2. Safely extract values with structural fallbacks
                    $title = $item['title'] ?? $item['task'] ?? $item['subject'] ?? 'Study Session';
                    $difficulty = $item['difficulty'] ?? 'Medium';
                    $date = $item['date'] ?? now()->toDateString();
                    $time = $item['time'] ?? 'Flexible';

                    // 3. Save directly to your Supabase instance
                    Schedule::create([
                        'user_id'    => $id,
                        'title'      => $title,
                        'difficulty' => $difficulty,
                        'date'       => $date,
                        'time'       => $time
                    ]);
                }
            }

            return response()->json([
                'message'  => 'Schedule has been generated',
                'schedule' => $scheduleData
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'AI Generation failed: ' . $e->getMessage()], 500);
        }
    }

    /*
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'     => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'difficulty'  => 'required|string|max:255',
            'date'        => 'required|string|max:255',
            'time'        => 'required|string|max:255'
        ]);

        if (!$data) {
            return response()->json([
                'message' => 'Validation error'
            ], 401);
        }

        $sched = Schedule::create([
            'user_id'     => $request->user_id,
            'title'       => $request->title,
            'difficulty'  => $request->difficulty,
            'date'        => $request->date,
            'time'        => $request->time
        ]);

        return response()->json([
            'message'     => 'Schedule has been saved',
            'schedule'    => $sched
        ], 201);
    }
    */

    public function fetch($id)
    {
        $sched = Schedule::find($id)->get();

        if (!$sched) {
            return response()->json([
                'message'  => 'No schedules found',
            ], 404);
        }

        return response()->json([
            'message'  => 'Schedule deleted',
            'schedule' => $sched
        ], 201);
    }

    public function delete($id)
    {
        $sched = Schedule::find($id);
        $sched->delete();

        return response()->json([
            'message' => 'Schedule deleted'
        ], 201);
    }
}
