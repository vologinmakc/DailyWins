<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = [
            'grant_type' => 'password',
            'client_id' => config('auth.passport.client_id'),
            'client_secret' => config('auth.passport.client_secret'),
            'username' => $request->username,
            'password' => $request->password,
            'scope' => '',
        ];

        $tokenRequest = Request::create('/oauth/token', 'POST', $data);

        // Обработайте этот запрос внутри вашего приложения
        $response = app()->handle($tokenRequest);

        return response()->json(json_decode($response->getContent()), $response->getStatusCode());
    }
}
