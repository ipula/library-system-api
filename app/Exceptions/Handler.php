<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (\App\Domain\Shared\Exceptions\DomainException $e, $request) {
            return response()->json([
                'error'   => class_basename($e),
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Token is missing or invalid.',
            ], 401);
        });

        $this->renderable(function (ValidationException $e, $request) {
                return response()->json([
                    'error' => 'Validation Error',
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
        });
    }
}
