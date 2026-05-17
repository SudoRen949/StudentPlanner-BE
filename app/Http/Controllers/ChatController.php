<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array'
        ]);

        $userMessage = $request->input('message');
        $chatHistory = $request->input('history', []);

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an elite, highly encouraging student assistant AI study coach. Help the student understand concepts clearly, provide concise breakdown answers, use bullet points where helpful, and stay casual.'
            ]
        ];

        foreach ($chatHistory as $chat) {
            $role = ( $chat['sender'] === 'user' ? 'user' : 'assistant' );
            $messages[] = [ 'role' => $role, 'content' => $chat['text'] ];
        }

        try {
            $apiKey = env('OPENAI_API_KEY');
            $baseUrl = env('OPENAI_BASE_URL');
            $guzzleClient = new \GuzzleHttp\Client([ 'verify' => false ]);

            $client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withBaseUri($baseUrl)
                ->withHttpClient($guzzleClient)
                ->make();

            $response = $client->chat()->create([
                'model' => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'temperature' => 0.7
            ]);

            $aiReply = $response->choices[0]->message->content;

            return response()->json([ 'reply' => $aiReply ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Sorry, I had trouble generating a response. Details: ' . $e->getMessage()
            ], 500);
        }
    }
}
