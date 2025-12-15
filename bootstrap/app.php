<?php

use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\ShopMiddleware;
use Illuminate\Foundation\Application;
use App\Http\Middleware\DemoMiddleware;
use App\Http\Middleware\UserAuthMiddleware;
use App\Http\Middleware\CheckAdminMiddleware;
use App\Http\Middleware\MaintenanceMiddleware;
use App\Http\Middleware\XSSProtectionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/user.php'));
            Route::middleware('web')->group(base_path('routes/auth.php'));
            Route::middleware('web')->group(base_path('routes/quote-admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            XSSProtectionMiddleware::class,
            DemoMiddleware::class,
            LanguageMiddleware::class,
        ])->alias([
            'checkAdmin' => CheckAdminMiddleware::class,
            'userAuth' => UserAuthMiddleware::class,
            'maintenance' => MaintenanceMiddleware::class,
            'demo' => DemoMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'shop' => ShopMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
