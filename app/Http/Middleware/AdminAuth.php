<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AdminAuth extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        return route('admin.login');
    }

    protected function guards(): array
    {
        return ['admin'];
    }

    public function handle($request, \Closure $next, ...$params)
    {
        return parent::handle($request, $next, ...$this->guards());
    }
}
