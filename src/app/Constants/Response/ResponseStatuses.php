<?php

namespace App\Constants\Response;

class ResponseStatuses
{
    const COMPLETE = 'COMPLETE';
    const ERROR    = 'ERROR';


    const MESSAGES = [
        self::COMPLETE => 'Операция полностью успешно выполнена.',
        self::ERROR    => 'Произошла ошибка при выполнении операции.'
    ];
}
