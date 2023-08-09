<?php

namespace App\Http\Controllers;

use App\Constants\Response\ResponseStatuses;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;

class BaseController extends Controller
{
    protected function response($data, $status = 200, $headers = [], $options = 0)
    {
        // Если в ответе содержится модель или коллекция моделей и у модели есть трейт Expandable
        if ($data instanceof Collection && $data->first() instanceof BaseModel) {
            $data = $data->map(function ($model) {
                return $model->expandFields(request());
            });
        } elseif ($data instanceof BaseModel) {
            $data = $data->expandFields(request());
        }

        $formattedResponse = [
            'result_code'    => ResponseStatuses::COMPLETE,
            'result_message' => ResponseStatuses::MESSAGES[ResponseStatuses::COMPLETE],
            'data'           => $data
        ];

        return response()->json($formattedResponse, $status, $headers, $options);
    }
}
