<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Connexion de l'utilisateur
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        
        // Charger les relations selon le rôle
        if ($user instanceof User && $user->role === 'student') {
            $user->load('student');
        } elseif ($user instanceof User && $user->role === 'teacher') {
            $user->load('teacher');
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
        ]);
    }

    /**
     * Inscription d'un nouvel utilisateur (réservé à l'admin ou ouvert)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
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
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Créer les données spécifiques selon le rôle
        if ($user instanceof User && $user->role === 'student') {
            Student::create([
                'user_id' => $user->id,
                'roll_number' => $validated['roll_number'],
                'class' => $validated['class'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'parent_phone' => $validated['parent_phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
            $user->load('student');
        } elseif ($user instanceof User && $user->role === 'teacher') {
            Teacher::create([
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'phone' => $validated['phone'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
            ]);
            $user->load('teacher');
        }

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
        ], 201);
    }

    /**
     * Déconnexion de l'utilisateur
     */
    // public function logout(Request $request)
    // {
    //     Auth::guard('web')->logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //      // Clear the session cookie by setting it to expire
    //     // Cookie::queue(Cookie::forget('school_management_session'));
    //     // Cookie::queue(Cookie::forget('XSRF-TOKEN'));

    //     return response()->json([
    //         'message' => 'Déconnexion réussie'
    //     ])->withHeaders([
    //     'Clear-Site-Data' => '"cookies", "storage"'
    // ]);
    // }

    public function logout(Request $request)
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Déconnexion réussie'
    ])->withCookie(Cookie::forget('school_management_session'))
      ->withCookie(Cookie::forget('XSRF-TOKEN'));
}

    /**
     * Récupérer l'utilisateur connecté
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        if ($user instanceof User && $user->role === 'student') {
            $user->load('student');
        } elseif ($user instanceof User && $user->role === 'teacher') {
            $user->load('teacher');
        }

        return response()->json($user);
    }
}