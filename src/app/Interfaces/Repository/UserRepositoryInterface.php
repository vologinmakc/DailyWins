<?php

namespace App\Interfaces\Repository;

use App\Services\Dto\User\UserDto;

interface UserRepositoryInterface
{
    public function create(UserDto $userDto);
}
