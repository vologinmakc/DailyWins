<?php

namespace App\Constants\Response;

use App\Constants\BaseConstant;

class ResponseStatuses extends BaseConstant
{
    const COMPLETE = 'COMPLETE';
    const ERROR    = 'ERROR';


    const MESSAGES = [
        self::COMPLETE => 'Операция полностью успешно выполнена.',
        self::ERROR    => 'Произошла ошибка при выполнении операции.'
    ];

    static function getList()
    {
        return [
            self::COMPLETE,
            self::ERROR
        ];
    }
}
