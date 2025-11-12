<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Task;
use App\Models\Grade;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Afficher le profil de l'étudiant connecté
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Accès réservé aux étudiants'
            ], 403);
        }

        $student = $user->student()->with('user')->first();

        return response()->json($student);
    }

    /**
     * Afficher les devoirs de l'étudiant connecté
     */
    public function tasks(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Accès réservé aux étudiants'
            ], 403);
        }

        $student = $user->student;
        $tasks = Task::where('class', $student->class)
            ->with('teacher.user')
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks);
    }

    /**
     * Afficher les notes de l'étudiant connecté
     */
    public function grades(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Accès réservé aux étudiants'
            ], 403);
        }

        $student = $user->student;
        $grades = Grade::where('student_id', $student->id)
            ->with(['teacher.user', 'task'])
            ->orderBy('graded_at', 'desc')
            ->get();

        return response()->json($grades);
    }

    /**
     * Afficher tous les étudiants (pour professeurs et admin)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isTeacher() && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $students = Student::with('user')->get();

        return response()->json($students);
    }

    /**
     * Afficher un étudiant spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isTeacher() && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $student = Student::with('user')->findOrFail($id);

        return response()->json($student);
    }
}