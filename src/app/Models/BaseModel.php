<?php

namespace App\Models;

use App\Trait\Model\Expandable;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use Expandable;
    protected static $expandableFields = [];

    public static function setExpandFields(array $fields)
    {
        static::$expandableFields = $fields;
    }
}
