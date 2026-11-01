<?php

use App\Helpers\ApiResponseHelper;
use App\Helpers\LogHelper;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'request.logger' => \App\Http\Middleware\RequestLogger::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'force.change.password' => \App\Http\Middleware\CheckForceChangePasswordMiddleware::class,
            'check.password.expiry' => \App\Http\Middleware\CheckPasswordExpiryMiddleware::class,
            'check.incoming.ip' => \App\Http\Middleware\CheckIncomingIp::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'user.security.audit' => \App\Http\Middleware\UserSecurityAuditTrack::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, $request) {
            if ($request->is('api/*')) {
                if ($exception instanceof AuthenticationException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 401 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::unauthenticated(__('messages.unauthenticated'));
                } elseif ($exception instanceof ClientException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 401 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::unauthenticated(__('messages.unauthenticated'));
                } elseif ($exception instanceof MethodNotAllowedHttpException) {
                    LogHelper::logEntry(LogHelper::LOG_CRITICAL, 'Handler.php 405 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::methodNotAllowed(__('messages.method_not_allowed_description'));
                } elseif ($exception instanceof NotFoundHttpException) {
                    LogHelper::logEntry(LogHelper::LOG_CRITICAL, 'Handler.php 404 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::notFound(__('messages.not_found_description'));
                } elseif ($exception instanceof AuthorizationException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 403 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::forbidden(__('messages.forbidden_description'));
                } elseif ($exception instanceof AccessDeniedHttpException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 403 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::forbidden(__('messages.forbidden_description'));
                } elseif ($exception instanceof UnauthorizedException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 403 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::forbidden(__('messages.forbidden_description'));
                } elseif ($exception instanceof ValidationException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 422 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::validationError($exception->validator->errors(), __('messages.validation_error'));
                } elseif ($exception instanceof ModelNotFoundException) {
                    LogHelper::logEntry(LogHelper::LOG_ERROR, 'Handler.php 404 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::notFound(__('messages.not_found_description'));
                } else {
                    LogHelper::logEntry(LogHelper::LOG_CRITICAL, 'Handler.php 500 - ' . $exception->getMessage(), $exception);
                    return ApiResponseHelper::serverError(__('messages.system_error_description'));
                }
            }
            return null;
        });
    })->create();
