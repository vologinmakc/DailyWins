<?php

namespace App\Http\Middleware\Response;

use App\Constants\Response\ResponseStatuses;
use Closure;
use Illuminate\Http\JsonResponse;

class FormatApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Проверяем, является ли ответ экземпляром JsonResponse
        if (!$response instanceof JsonResponse) {
            return $response;
        }

        $data = $response->getData(true);

        // Проверяем, отформатирован ли ответ
        if (isset($data['result_code'], $data['result_message'], $data['data'])) {
            // Если ответ уже отформатирован, просто возвращаем его без изменений
            // т к ошибки обрабатываются в обработчике Handler Exception
            return $response;
        }

        return response()->json([
            'result_code'    => ResponseStatuses::COMPLETE,
            'result_message' => ResponseStatuses::MESSAGES[ResponseStatuses::COMPLETE],
            'data'           => $data ?? null
        ]);
    }
}
