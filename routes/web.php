<?php



// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\StudentController;
// use App\Http\Controllers\TeacherController;
// use App\Http\Controllers\TaskController;
// use App\Http\Controllers\GradeController;
// use App\Http\Controllers\AdminController;

// /*
// |--------------------------------------------------------------------------
// | Web Routes (API Routes avec Session Authentication)
// |--------------------------------------------------------------------------
// */

// // ✅ PUBLIC ROUTES - No authentication required
// Route::get('/sanctum/csrf-cookie', function () {
//     return response()->json(['message' => 'CSRF cookie set']);
// });

// // Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // ✅ PROTECTED ROUTES - Require authentication
// Route::middleware('auth')->group(function () {
    
//     // Auth routes
//     // Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/user', [AuthController::class, 'user']);
//      Route::post('/logout', [AuthController::class, 'logout']);
    
//     // Routes communes à tous les utilisateurs authentifiés
//     Route::get('/tasks', [TaskController::class, 'index']);
//     Route::get('/tasks/{id}', [TaskController::class, 'show']);
//     Route::get('/grades', [GradeController::class, 'index']);
//     Route::get('/grades/{id}', [GradeController::class, 'show']);
    
//     // Routes spécifiques aux étudiants
//     Route::middleware('role:student')->group(function () {
//         Route::get('/student/profile', [StudentController::class, 'profile']);
//         Route::get('/student/tasks', [StudentController::class, 'tasks']);
//         Route::get('/student/grades', [StudentController::class, 'grades']);
//     });
    
//     // Routes spécifiques aux professeurs
//     Route::middleware('role:teacher')->group(function () {
//         Route::get('/teacher/profile', [TeacherController::class, 'profile']);
//         Route::get('/teacher/students', [TeacherController::class, 'students']);
        
//         // Gestion des devoirs
//         Route::post('/tasks', [TaskController::class, 'store']);
//         Route::put('/tasks/{id}', [TaskController::class, 'update']);
//         Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
        
//         // Gestion des notes
//         Route::post('/grades', [GradeController::class, 'store']);
//         Route::put('/grades/{id}', [GradeController::class, 'update']);
//         Route::delete('/grades/{id}', [GradeController::class, 'destroy']);
//         Route::get('/grades/student/{studentId}', [GradeController::class, 'studentGrades']);
//     });
    
//     // Routes spécifiques aux administrateurs
//     Route::middleware('role:admin')->group(function () {
//         // Gestion des utilisateurs
//         Route::post('/register', [AuthController::class, 'register']);
//         Route::get('/admin/users', [AdminController::class, 'users']);
//         Route::post('/admin/users', [AdminController::class, 'createUser']);
//         Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
//         Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
        
//         // Statistiques
//         Route::get('/admin/statistics', [AdminController::class, 'statistics']);
        
//         // Voir tous les étudiants et professeurs
//         Route::get('/admin/students', [StudentController::class, 'index']);
//         Route::get('/admin/students/{id}', [StudentController::class, 'show']);
//         Route::get('/admin/teachers', [TeacherController::class, 'index']);
//         Route::get('/admin/teachers/{id}', [TeacherController::class, 'show']);
//     });

 
// });


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes with /api prefix
|--------------------------------------------------------------------------
*/

// Wrap ALL routes in /api prefix
Route::prefix('api')->group(function () {
    
    // ✅ PUBLIC ROUTES - No authentication required
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });
    
    Route::post('/login', [AuthController::class, 'login']);
    
    // ✅ PROTECTED ROUTES - Require authentication
    Route::middleware('auth')->group(function () {
        
        // Auth routes
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Routes communes à tous les utilisateurs authentifiés
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{id}', [TaskController::class, 'show']);
        Route::get('/grades', [GradeController::class, 'index']);
        Route::get('/grades/{id}', [GradeController::class, 'show']);
        
        // Routes spécifiques aux étudiants
        Route::middleware('role:student')->group(function () {
            Route::get('/student/profile', [StudentController::class, 'profile']);
            Route::get('/student/tasks', [StudentController::class, 'tasks']);
            Route::get('/student/grades', [StudentController::class, 'grades']);
        });
        
        // Routes spécifiques aux professeurs
        Route::middleware('role:teacher')->group(function () {
            Route::get('/teacher/profile', [TeacherController::class, 'profile']);
            Route::get('/teacher/students', [TeacherController::class, 'students']);
            
            // Gestion des devoirs
            Route::post('/tasks', [TaskController::class, 'store']);
            Route::put('/tasks/{id}', [TaskController::class, 'update']);
            Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
            
            // Gestion des notes
            Route::post('/grades', [GradeController::class, 'store']);
            Route::put('/grades/{id}', [GradeController::class, 'update']);
            Route::delete('/grades/{id}', [GradeController::class, 'destroy']);
            Route::get('/grades/student/{studentId}', [GradeController::class, 'studentGrades']);
        });
        
        // Routes spécifiques aux administrateurs
        Route::middleware('role:admin')->group(function () {
            // Gestion des utilisateurs
            Route::post('/register', [AuthController::class, 'register']);
            Route::get('/admin/users', [AdminController::class, 'users']);
            Route::post('/admin/users', [AdminController::class, 'createUser']);
            Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
            Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
            
            // Statistiques
            Route::get('/admin/statistics', [AdminController::class, 'statistics']);
            
            // Voir tous les étudiants et professeurs
            Route::get('/admin/students', [StudentController::class, 'index']);
            Route::get('/admin/students/{id}', [StudentController::class, 'show']);
            Route::get('/admin/teachers', [TeacherController::class, 'index']);
            Route::get('/admin/teachers/{id}', [TeacherController::class, 'show']);
        });
    });
});

// Keep sanctum route at root level for compatibility
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});