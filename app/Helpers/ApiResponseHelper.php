<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ApiResponseHelper
{
    public static function response($code = 200, ?string $message = null, $data = null, $errors = null): JsonResponse
    {
        if (!is_null($errors)) {
            $message = is_array($errors)
                ? reset($errors)[0]
                : $errors->first();
        }

        if ($data instanceof JsonResource) {
            $response = $data->additional([
                'message' => $message,
                'errors' => $errors,
                'code' => $code,
            ])->response()->getData(true);
        } else {
            $response = [
                'code' => $code,
                'message' => $message,
                'errors' => $errors,
                'data' => $data,
            ];
        }

        $response = response()->json($response);

        LogHelper::logEntry(LogHelper::LOG_INFO, 'API Response: ' . $response);

        return $response;
    }

    public static function success($data = null, ?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_OK, $message ?? __('Success'), $data);
    }

    public static function validationError($errors, $message = null): JsonResponse
    {
        return self::response(Response::HTTP_UNPROCESSABLE_ENTITY, $message ?? __('messages.validation_error'), null, $errors);
    }

    public static function notFound(?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_NOT_FOUND, $message ?? __('messages.not_found_description'));
    }

    public static function badRequest(?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_BAD_REQUEST, $message ?? __('messages.bad_request_description'));
    }

    public static function methodNotAllowed(?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_METHOD_NOT_ALLOWED, $message ?? __('messages.method_not_allowed_description'));
    }

    public static function unauthenticated(?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_UNAUTHORIZED, $message ?? __('messages.unauthenticated_description'));
    }

    public static function forbidden(?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_FORBIDDEN, $message ?? __('messages.forbidden_description'));
    }

    public static function serverError(?string $message = null): JsonResponse
    {
        return self::response(Response::HTTP_INTERNAL_SERVER_ERROR, $message ?? __('messages.system_error_description'));
    }

    public static function error(string $message, int $code = 400, $data = null): JsonResponse
    {
        return self::response($code, $message, $data);
    }

    public static function fetchAndThrow($data): JsonResponse
    {
        $response = response()->json($data);

        LogHelper::logEntry(LogHelper::LOG_INFO, 'API Response: ' . $response);

        return $response;
    }
}
