<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        });
        $exceptions->renderable(function (\App\Domain\Shared\Exceptions\DomainException $e, $request) {
            return response()->json([
                'error'   => class_basename($e),
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        });

        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Token is missing or invalid.',
            ], 401);
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e, $request) {
            return response()->json([
                'error' => 'Invalid credentials',
                'message' => 'Email or password is incorrect.',
            ], 401);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
                return response()->json([
                    'error' => 'NotFound',
                    'message' => $e->getMessage() ?: 'Resource not found',
                ], 404);
        });
//        $exceptions->render(function (Throwable $e, $request) {
//                report($e);
//                return response()->json([
//                    'error' => 'ServerError',
//                    'message' => 'Something went wrong.',
//                ], 500);
//        });
    })->create();
