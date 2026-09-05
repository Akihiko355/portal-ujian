<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
        $middleware->redirectGuestsTo(fn ($request) => $request->is('admin/*') ? route('admin.login') : route('user.login'));
        $middleware->redirectUsersTo(fn ($request) => $request->is('admin/*') ? route('admin.dashboard') : route('user.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login');
            }
            return redirect()->route('user.login');
        });
    })->create();
