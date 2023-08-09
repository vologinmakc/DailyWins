<?php

namespace App\Exceptions;

use App\Constants\Response\ResponseStatuses;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        return response()->json([
            'result_code' => ResponseStatuses::ERROR,
            'result_message' => ResponseStatuses::MESSAGES[ResponseStatuses::ERROR],
            'data' => $e->getMessage()
        ], 500);
    }
}
