<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Afficher tous les utilisateurs
     */
    public function users(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        $users = User::with(['student', 'teacher'])->get();

        return response()->json($users);
    }

    /**
     * Créer un nouvel utilisateur (admin uniquement)
     */
    public function createUser(Request $request)
    {

         // Log for debugging
    \Log::info('Create User Request', [
        'origin' => $request->header('Origin'),
        'method' => $request->method(),
        'cookies' => $request->cookies->all(),
    ]);
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:student,teacher,admin',
            
            // Champs spécifiques aux étudiants
            'roll_number' => 'required_if:role,student|unique:students,roll_number',
            'class' => 'required_if:role,student|string|max:50',
            'date_of_birth' => 'nullable|date',
            'parent_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            
            // Champs spécifiques aux professeurs
            'subject' => 'required_if:role,teacher|string|max:100',
            'phone' => 'nullable|string|max:20',
            'qualification' => 'nullable|string|max:255',
        ]);

        // Créer l'utilisateur
        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Créer les données spécifiques selon le rôle
        if ($newUser->isStudent()) {
            Student::create([
                'user_id' => $newUser->id,
                'roll_number' => $validated['roll_number'],
                'class' => $validated['class'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'parent_phone' => $validated['parent_phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
            $newUser->load('student');
        } elseif ($newUser->isTeacher()) {
            Teacher::create([
                'user_id' => $newUser->id,
                'subject' => $validated['subject'],
                'phone' => $validated['phone'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
            ]);
            $newUser->load('teacher');
        }

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $newUser,
        ], 201);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        $targetUser = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'sometimes|required|in:student,teacher,admin',
            
            // Champs spécifiques aux étudiants
            'roll_number' => 'sometimes|required|unique:students,roll_number,' . ($targetUser->student->id ?? 'NULL'),
            'class' => 'sometimes|required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'parent_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            
            // Champs spécifiques aux professeurs
            'subject' => 'sometimes|required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'qualification' => 'nullable|string|max:255',
        ]);

        // Mettre à jour les informations de base
        $updateData = [];
        if (isset($validated['name'])) $updateData['name'] = $validated['name'];
        if (isset($validated['email'])) $updateData['email'] = $validated['email'];
        if (isset($validated['password'])) $updateData['password'] = Hash::make($validated['password']);
        if (isset($validated['role'])) $updateData['role'] = $validated['role'];

        $targetUser->update($updateData);

        // Mettre à jour les données spécifiques selon le rôle
        if ($targetUser->isStudent() && $targetUser->student) {
            $studentData = [];
            if (isset($validated['roll_number'])) $studentData['roll_number'] = $validated['roll_number'];
            if (isset($validated['class'])) $studentData['class'] = $validated['class'];
            if (isset($validated['date_of_birth'])) $studentData['date_of_birth'] = $validated['date_of_birth'];
            if (isset($validated['parent_phone'])) $studentData['parent_phone'] = $validated['parent_phone'];
            if (isset($validated['address'])) $studentData['address'] = $validated['address'];
            
            $targetUser->student->update($studentData);
            $targetUser->load('student');
        } elseif ($targetUser->isTeacher() && $targetUser->teacher) {
            $teacherData = [];
            if (isset($validated['subject'])) $teacherData['subject'] = $validated['subject'];
            if (isset($validated['phone'])) $teacherData['phone'] = $validated['phone'];
            if (isset($validated['qualification'])) $teacherData['qualification'] = $validated['qualification'];
            
            $targetUser->teacher->update($teacherData);
            $targetUser->load('teacher');
        }

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $targetUser,
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        // Ne pas permettre à l'admin de se supprimer lui-même
        if ($user->id == $id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte'
            ], 422);
        }

        $targetUser = User::findOrFail($id);
        $targetUser->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    /**
     * Statistiques du système
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        $stats = [
            'total_users' => User::count(),
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_tasks' => \App\Models\Task::count(),
            'total_grades' => \App\Models\Grade::count(),
        ];

        return response()->json($stats);
    }
}