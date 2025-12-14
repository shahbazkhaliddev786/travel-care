<?php

namespace App\Http\Controllers;

use App\Services\AgoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgoraController extends Controller
{
    public function generateToken(Request $request, AgoraService $agora)
    {
        $validated = $request->validate([
            'channel' => ['required', 'string'],
            'uid' => ['required'],
        ]);

        $channel = Str::slug($validated['channel'], '_');
        $uid = $validated['uid'];

        $token = $agora->generateRtcToken($channel, $uid);

        return response()->json([
            'appId' => config('services.agora.app_id'),
            'channel' => $channel,
            'uid' => $uid,
            'token' => $token,
        ]);
    }
} 