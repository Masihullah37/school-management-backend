<?php




// // bootstrap/app.php - NE CHANGEZ RIEN !

// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->web(append: [
//             \App\Http\Middleware\CheckRole::class,
//         ]);
//     })
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);
//         // ✅ Ceci gère CORS automatiquement pour les domaines stateful
//         $middleware->statefulApi();
        
//         $middleware->alias([
//             'role' => \App\Http\Middleware\CheckRole::class,
//         ]);

//         $middleware->validateCsrfTokens(except: []);

//         $middleware->trustProxies(at: '*');
//     })
//     ->withExceptions(function (Exceptions $exceptions) {
//         //
//     })->create();





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

         // Add EncryptCookies to web group (important for sessions)
        // $middleware->web(append: [
        //     \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        // ]);

        // $middleware->validateCsrfTokens(except: [
        //     'signup','login','logout'
        // ]);
     
        
        // $middleware->web(append: [
        //     \App\Http\Middleware\CheckRole::class,
        // ]);
        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

    })
    
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();