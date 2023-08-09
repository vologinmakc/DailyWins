<?php

namespace App\Rules;

abstract class Rule
{
    abstract public function apply($query);
}
