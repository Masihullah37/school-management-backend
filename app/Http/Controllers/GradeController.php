<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Afficher toutes les notes (selon le rôle)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->isStudent()) {
            // L'étudiant voit seulement ses propres notes
            $student = $user->student;
            $grades = Grade::where('student_id', $student->id)
                ->with(['teacher.user', 'task', 'student.user'])
                ->orderBy('graded_at', 'desc')
                ->get();
        } elseif ($user->isTeacher()) {
            // Le professeur voit seulement les notes qu'il a données
            $teacher = $user->teacher;
            $grades = Grade::where('teacher_id', $teacher->id)
                ->with(['teacher.user', 'task', 'student.user'])
                ->orderBy('graded_at', 'desc')
                ->get();
        } else {
            // L'admin voit toutes les notes
            $grades = Grade::with(['teacher.user', 'task', 'student.user'])
                ->orderBy('graded_at', 'desc')
                ->get();
        }

        return response()->json($grades);
    }

    /**
     * Créer une nouvelle note (professeur uniquement)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Seuls les professeurs peuvent donner des notes'
            ], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'task_id' => 'nullable|exists:tasks,id',
            'subject' => 'required|string|max:100',
            'grade' => 'required|numeric|min:0',
            'max_grade' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
            'graded_at' => 'required|date',
        ]);

        // Vérifier que la note ne dépasse pas la note maximale
        if ($validated['grade'] > $validated['max_grade']) {
            return response()->json([
                'message' => 'La note ne peut pas dépasser la note maximale'
            ], 422);
        }

        $teacher = $user->teacher;

        $grade = Grade::create([
            'student_id' => $validated['student_id'],
            'teacher_id' => $teacher->id,
            'task_id' => $validated['task_id'] ?? null,
            'subject' => $validated['subject'],
            'grade' => $validated['grade'],
            'max_grade' => $validated['max_grade'],
            'comments' => $validated['comments'] ?? null,
            'graded_at' => $validated['graded_at'],
        ]);

        $grade->load(['teacher.user', 'task', 'student.user']);

        return response()->json([
            'message' => 'Note créée avec succès',
            'grade' => $grade
        ], 201);
    }

    /**
     * Afficher une note spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $grade = Grade::with(['teacher.user', 'task', 'student.user'])->findOrFail($id);

        // Vérifier les permissions
        if ($user->isStudent()) {
            $student = $user->student;
            if ($grade->student_id !== $student->id) {
                return response()->json([
                    'message' => 'Vous ne pouvez pas voir cette note'
                ], 403);
            }
        } elseif ($user->isTeacher()) {
            $teacher = $user->teacher;
            if ($grade->teacher_id !== $teacher->id) {
                return response()->json([
                    'message' => 'Vous ne pouvez pas voir cette note'
                ], 403);
            }
        }

        return response()->json($grade);
    }

    /**
     * Mettre à jour une note (professeur propriétaire uniquement)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Seuls les professeurs peuvent modifier des notes'
            ], 403);
        }

        $grade = Grade::findOrFail($id);
        $teacher = $user->teacher;

        // Vérifier que le professeur est le propriétaire
        if ($grade->teacher_id !== $teacher->id) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que vos propres notes'
            ], 403);
        }

        $validated = $request->validate([
            'student_id' => 'sometimes|required|exists:students,id',
            'task_id' => 'nullable|exists:tasks,id',
            'subject' => 'sometimes|required|string|max:100',
            'grade' => 'sometimes|required|numeric|min:0',
            'max_grade' => 'sometimes|required|numeric|min:0',
            'comments' => 'nullable|string',
            'graded_at' => 'sometimes|required|date',
        ]);

        // Vérifier que la note ne dépasse pas la note maximale
        if (isset($validated['grade']) && isset($validated['max_grade']) && $validated['grade'] > $validated['max_grade']) {
            return response()->json([
                'message' => 'La note ne peut pas dépasser la note maximale'
            ], 422);
        }

        $grade->update($validated);
        $grade->load(['teacher.user', 'task', 'student.user']);

        return response()->json([
            'message' => 'Note mise à jour avec succès',
            'grade' => $grade
        ]);
    }

    /**
     * Supprimer une note (professeur propriétaire uniquement)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Seuls les professeurs peuvent supprimer des notes'
            ], 403);
        }

        $grade = Grade::findOrFail($id);
        $teacher = $user->teacher;

        // Vérifier que le professeur est le propriétaire
        if ($grade->teacher_id !== $teacher->id) {
            return response()->json([
                'message' => 'Vous ne pouvez supprimer que vos propres notes'
            ], 403);
        }

        $grade->delete();

        return response()->json([
            'message' => 'Note supprimée avec succès'
        ]);
    }

    /**
     * Obtenir les notes d'un étudiant spécifique (pour professeurs et admin)
     */
    public function studentGrades(Request $request, $studentId)
    {
        $user = $request->user();
        
        if (!$user->isTeacher() && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $student = Student::findOrFail($studentId);
        $grades = Grade::where('student_id', $student->id)
            ->with(['teacher.user', 'task'])
            ->orderBy('graded_at', 'desc')
            ->get();

        return response()->json([
            'student' => $student->load('user'),
            'grades' => $grades
        ]);
    }
}