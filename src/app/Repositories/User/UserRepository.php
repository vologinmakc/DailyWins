<?php

namespace App\Repositories\User;

use App\Interfaces\Repository\UserRepositoryInterface;
use App\Models\User;
use App\Services\Dto\User\UserDto;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(UserDto $userDto)
    {
        $user = new User([
            'name'     => $userDto->name,
            'email'    => $userDto->email,
            'password' => Hash::make($userDto->password)
        ]);

        $user->save();

        return $user;
    }
}
