<?php

namespace App\Helpers;

use App\Enums\NpsbResponseCodeEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class NpsbResponseHelper
{

    public static function serverError()
    {
        $response = response()->json([
            'data' => ['responseCode' => NpsbResponseCodeEnum::MESSAGE_FORMAT_ERROR->value],
            'message' => [trans('messages.internal_error')],
            'errors' => [],
            'code' => 422 // If any issue not send 500, message formatter error
        ], 200);
        LogHelper::logEntry(LogHelper::LOG_ERROR, 'API Response: ' . $response);
        return $response;
    }

    public static function response($code = 200, $message = [], $data = null, $errors = [], $writeFullLog = true)
    {
        $errors = collect($errors)->flatten()->toArray();
        $errors = empty($errors) ? [] : $errors;
        $message = count($errors) ? $errors : $message;
        $message = !is_array($message) ? (array)$message : $message;

        if ($data instanceof JsonResource) {
            $response = $data->additional([
                'message' => $message,
                'errors' => $errors,
                'code' => $code
            ])->response()->getData(true);
        } else {
            $response = [
                'data' => $data,
                'message' => $message,
                'errors' => $errors,
                'code' => $code,

            ];
        }
        $response = response()->json($response);
        if ($writeFullLog) {
            LogHelper::logEntry(LogHelper::LOG_INFO, 'API Response: ' . $response);
        } else {
            LogHelper::logEntry(LogHelper::LOG_INFO, 'API Response: ' . 'Writing full log is turned off');
        }
        return $response;
    }

    public static function error($errors, $data = null, $message = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        return self::response($code, $message, $data, $errors);
    }

    public static function fetchAndThrow($data): JsonResponse
    {
        $response = response()->json($data);
        LogHelper::logEntry(LogHelper::LOG_INFO, 'API Response: ' . $response);
        return $response;
    }
}
