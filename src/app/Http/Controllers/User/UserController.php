<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Http\Requests\User\RegisterUserRequest;
use App\Repositories\User\UserRepository;
use App\Services\Dto\User\UserDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function me(Request $request)
    {
        return $this->response(Auth::user());
    }

    public function register(RegisterUserRequest $request)
    {
        $user = (new UserRepository())->create(new UserDto($request->validationData()));

        $token = $user->createToken('forFront')->accessToken;

        return $this->response([
            'user'  => $user,
            'token' => $token
        ]);
    }
}
