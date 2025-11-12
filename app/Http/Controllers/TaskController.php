<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Afficher tous les devoirs (selon le rôle)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->isStudent()) {
            // L'étudiant voit seulement les devoirs de sa classe
            $student = $user->student;
            $tasks = Task::where('class', $student->class)
                ->with('teacher.user')
                ->orderBy('due_date', 'asc')
                ->get();
        } elseif ($user->isTeacher()) {
            // Le professeur voit seulement ses propres devoirs
            $teacher = $user->teacher;
            $tasks = Task::where('teacher_id', $teacher->id)
                ->with('teacher.user')
                ->orderBy('due_date', 'asc')
                ->get();
        } else {
            // L'admin voit tous les devoirs
            $tasks = Task::with('teacher.user')
                ->orderBy('due_date', 'asc')
                ->get();
        }

        return response()->json($tasks);
    }

    /**
     * Créer un nouveau devoir (professeur uniquement)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Seuls les professeurs peuvent créer des devoirs'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string|max:100',
            'class' => 'required|string|max:50',
            'due_date' => 'nullable|date',
        ]);

        $teacher = $user->teacher;

        $task = Task::create([
            'teacher_id' => $teacher->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $validated['subject'],
            'class' => $validated['class'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        $task->load('teacher.user');

        return response()->json([
            'message' => 'Devoir créé avec succès',
            'task' => $task
        ], 201);
    }

    /**
     * Afficher un devoir spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::with('teacher.user')->findOrFail($id);

        // Vérifier les permissions
        if ($user->isStudent()) {
            $student = $user->student;
            if ($task->class !== $student->class) {
                return response()->json([
                    'message' => 'Vous ne pouvez pas voir ce devoir'
                ], 403);
            }
        } elseif ($user->isTeacher()) {
            $teacher = $user->teacher;
            if ($task->teacher_id !== $teacher->id) {
                return response()->json([
                    'message' => 'Vous ne pouvez pas voir ce devoir'
                ], 403);
            }
        }

        return response()->json($task);
    }

    /**
     * Mettre à jour un devoir (professeur propriétaire uniquement)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Seuls les professeurs peuvent modifier des devoirs'
            ], 403);
        }

        $task = Task::findOrFail($id);
        $teacher = $user->teacher;

        // Vérifier que le professeur est le propriétaire
        if ($task->teacher_id !== $teacher->id) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que vos propres devoirs'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'subject' => 'sometimes|required|string|max:100',
            'class' => 'sometimes|required|string|max:50',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);
        $task->load('teacher.user');

        return response()->json([
            'message' => 'Devoir mis à jour avec succès',
            'task' => $task
        ]);
    }

    /**
     * Supprimer un devoir (professeur propriétaire uniquement)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isTeacher()) {
            return response()->json([
                'message' => 'Seuls les professeurs peuvent supprimer des devoirs'
            ], 403);
        }

        $task = Task::findOrFail($id);
        $teacher = $user->teacher;

        // Vérifier que le professeur est le propriétaire
        if ($task->teacher_id !== $teacher->id) {
            return response()->json([
                'message' => 'Vous ne pouvez supprimer que vos propres devoirs'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Devoir supprimé avec succès'
        ]);
    }
}
