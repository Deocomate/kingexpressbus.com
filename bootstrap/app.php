<?php

use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\AdminDatabaseTransaction;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Guest group: only login/logout routes
            Route::middleware(['web', 'throttle:admin-auth'])
                ->prefix('quan-tri')
                ->name('admin.')
                ->group(base_path('routes/admin/auth.php'));

            // Authenticated + transactional group: all module routes except auth and ui
            Route::middleware(['web', AuthenticateSession::class, 'admin.auth', 'admin.transaction'])
                ->prefix('quan-tri')
                ->name('admin.')
                ->group(function () {
                    foreach (glob(base_path('routes/admin/*.php')) as $file) {
                        $base = basename($file);
                        if ($base !== 'auth.php' && $base !== 'ui.php') {
                            require $file;
                        }
                    }
                });

            // Authenticated non-transactional group: options, upload, ui-kit demo
            // Upload/options must not hold a DB lock during file I/O.
            Route::middleware(['web', AuthenticateSession::class, 'admin.auth'])
                ->prefix('quan-tri')
                ->name('admin.')
                ->group(base_path('routes/admin/ui.php'));

            // Legacy Filament /admin bookmarks → /quan-tri (after panel removal)
            Route::middleware('web')
                ->group(base_path('routes/admin-legacy-redirect.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(
            except: [
                'checkout/sepay/ipn',
            ]
        );
        $middleware->appendToGroup('web', SetLocale::class);
        $middleware->alias([
            'admin.auth' => AdminAuthMiddleware::class,
            'admin.transaction' => AdminDatabaseTransaction::class,
        ]);
        // Authz before route-model binding so non-admins get 403, not 404 leakage.
        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: AdminAuthMiddleware::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
