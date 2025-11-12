<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Afficher le profil du professeur connecté
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Accès réservé aux professeurs'
            ], 403);
        }

        $teacher = $user->teacher()->with('user')->first();

        return response()->json($teacher);
    }

    /**
     * Afficher tous les étudiants (pour le professeur)
     */
    public function students(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Accès réservé aux professeurs'
            ], 403);
        }

        $students = Student::with('user')->get();

        return response()->json($students);
    }

    /**
     * Afficher tous les professeurs (pour admin)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        $teachers = Teacher::with('user')->get();

        return response()->json($teachers);
    }

    /**
     * Afficher un professeur spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        $teacher = Teacher::with('user')->findOrFail($id);

        return response()->json($teacher);
    }
}