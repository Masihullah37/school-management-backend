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

         // Add CORS middleware if needed
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

           // Keep it simple - just use Laravel's built-in CORS
        $middleware->statefulApi();

        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

    })
    
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();