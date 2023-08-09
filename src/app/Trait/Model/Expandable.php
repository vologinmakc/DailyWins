<?php

namespace App\Trait\Model;

use Illuminate\Http\Request;

trait Expandable
{
    protected static $expandableFields = [];

    public static function setExpandField(array $fields)
    {
        static::$expandableFields = array_merge(static::$expandableFields, $fields);
    }

    public function getExpandedData($field)
    {
        if (isset(static::$expandableFields[$field])) {
            $callback = static::$expandableFields[$field];
            return $callback($this);
        }

        return null;
    }

    public function expandFields(Request $request)
    {
        $expandedData = $this->toArray();

        if ($expandFields = $request->input('expand', [])) {
            $expandFields = explode(',', $expandFields);

            foreach ($expandFields as $field) {
                if (isset(static::$expandableFields[$field])) {
                    $expandedData[$field] = call_user_func(static::$expandableFields[$field], $this);
                }
            }
        }

        return $expandedData;
    }
}
