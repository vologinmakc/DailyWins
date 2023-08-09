<?php

namespace App\Rules\Task;

use App\Rules\Rule;
use Illuminate\Support\Facades\Auth;

class UserRule extends Rule
{
    public function apply($query)
    {
        return $query->where('created_by', Auth::id());
    }
}

