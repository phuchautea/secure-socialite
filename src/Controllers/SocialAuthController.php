<?php

namespace SecureSocialite\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController
{
    public function redirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'redirect' => 'required|url',
            'provider' => 'required|in:google',
            'nonce' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $stateId = Str::uuid()->toString();

        $payload = [
            'id' => $stateId,
            'redirect' => $request->input('redirect'),
            'provider' => $request->input('provider'),
            'nonce' => $request->input('nonce'),
            'timestamp' => now()->timestamp,
        ];

        $encrypted = Crypt::encryptString(json_encode($payload));
        Cache::put("oauth_state:$stateId", $encrypted, 300);

        return Socialite::driver($request->input('provider'))
            ->stateless()
            ->with(['state' => $stateId])
            ->redirect();
    }

    public function callback(Request $request)
    {
        $stateId = $request->input('state');
        $encrypted = Cache::pull("oauth_state:$stateId");

        if (!$encrypted) {
            return response()->json(['error' => 'Invalid or expired state'], 400);
        }

        $state = json_decode(Crypt::decryptString($encrypted), true);

        if (now()->timestamp - $state['timestamp'] > 300) {
            return response()->json(['error' => 'State expired'], 400);
        }

        $allowed = config('secure-socialite.whitelist');
        $host = parse_url($state['redirect'], PHP_URL_HOST);
        if (!in_array($host, $allowed)) {
            return response()->json(['error' => 'Redirect not allowed'], 400);
        }

        $socialUser = Socialite::driver($state['provider'])->stateless()->user();

        $user = User::updateOrCreate(
            [$state['provider'].'_id' => $socialUser->id],
            [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'avatar' => $socialUser->avatar,
            ]
        );

        $token = JWTAuth::fromUser($user);

        return redirect()->to($state['redirect'] . '?token=' . $token);
    }
}
