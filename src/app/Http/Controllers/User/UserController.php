<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function me()
    {
        return $this->response(Auth::user());
    }
}
