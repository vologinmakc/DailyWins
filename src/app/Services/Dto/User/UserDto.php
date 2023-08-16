<?php

namespace App\Services\Dto\User;

use App\Services\Dto\BaseDto;

class UserDto extends BaseDto
{
    public ?string $name;
    public ?string $email;
    public ?string $password;
}
