<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // bootstrap/app.php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        // 401 unauthenticated
$exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }
});

// 422 validation error
$exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors(),
        ], 422);
    }
});

// 405, 404, dll
$exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'message' => $e->getMessage() ?: 'HTTP error',
            'status' => $e->getStatusCode(),
        ], $e->getStatusCode());
    }
});

// 500 fallback
$exceptions->render(function (\Throwable $e, $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'message' => 'Internal Server Error'
        ], 500);
    }
});

    })->create();
