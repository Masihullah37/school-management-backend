<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Task;
use App\Models\Grade;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un administrateur
        $admin = User::create([
            'name' => 'Administrateur Principal',
            'email' => 'admin@school.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Créer des professeurs
        $teacher1 = User::create([
            'name' => 'Monsieur Dupont',
            'email' => 'dupont@school.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        $teacherProfile1 = Teacher::create([
            'user_id' => $teacher1->id,
            'subject' => 'Mathématiques',
            'phone' => '+33612345678',
            'qualification' => 'Master en Mathématiques',
        ]);

        $teacher2 = User::create([
            'name' => 'Madame Martin',
            'email' => 'martin@school.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        $teacherProfile2 = Teacher::create([
            'user_id' => $teacher2->id,
            'subject' => 'Français',
            'phone' => '+33612345679',
            'qualification' => 'Master en Lettres Modernes',
        ]);

        // Créer des étudiants
        $student1 = User::create([
            'name' => 'Marie Dubois',
            'email' => 'marie.dubois@school.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $studentProfile1 = Student::create([
            'user_id' => $student1->id,
            'roll_number' => 'STU001',
            'class' => '5ème B',
            'date_of_birth' => '2010-05-15',
            'parent_phone' => '+33698765432',
            'address' => '123 Rue de la Paix, Paris',
        ]);

        $student2 = User::create([
            'name' => 'Pierre Lambert',
            'email' => 'pierre.lambert@school.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $studentProfile2 = Student::create([
            'user_id' => $student2->id,
            'roll_number' => 'STU002',
            'class' => '5ème B',
            'date_of_birth' => '2010-08-22',
            'parent_phone' => '+33698765433',
            'address' => '456 Avenue des Champs, Paris',
        ]);

        $student3 = User::create([
            'name' => 'Sophie Bernard',
            'email' => 'sophie.bernard@school.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $studentProfile3 = Student::create([
            'user_id' => $student3->id,
            'roll_number' => 'STU003',
            'class' => '6ème A',
            'date_of_birth' => '2009-03-10',
            'parent_phone' => '+33698765434',
            'address' => '789 Boulevard Saint-Michel, Paris',
        ]);

        // Créer des devoirs
        $task1 = Task::create([
            'teacher_id' => $teacherProfile1->id,
            'title' => 'Exercices de Géométrie',
            'description' => 'Résoudre les exercices de la page 45, questions 1 à 10. Tracer les figures demandées avec précision.',
            'subject' => 'Mathématiques',
            'class' => '5ème B',
            'due_date' => now()->addDays(7),
        ]);

        $task2 = Task::create([
            'teacher_id' => $teacherProfile1->id,
            'title' => 'Problèmes d\'Algèbre',
            'description' => 'Résoudre les équations du premier degré de la page 58.',
            'subject' => 'Mathématiques',
            'class' => '5ème B',
            'due_date' => now()->addDays(10),
        ]);

        $task3 = Task::create([
            'teacher_id' => $teacherProfile2->id,
            'title' => 'Lecture et Résumé',
            'description' => 'Lire le chapitre 3 du livre "Le Petit Prince" et écrire un résumé de 200 mots.',
            'subject' => 'Français',
            'class' => '5ème B',
            'due_date' => now()->addDays(5),
        ]);

        $task4 = Task::create([
            'teacher_id' => $teacherProfile2->id,
            'title' => 'Analyse Grammaticale',
            'description' => 'Analyser les phrases de l\'exercice 12 page 67 : identifier les COD, COI et compléments circonstanciels.',
            'subject' => 'Français',
            'class' => '6ème A',
            'due_date' => now()->addDays(8),
        ]);

        // Créer des notes
        Grade::create([
            'student_id' => $studentProfile1->id,
            'teacher_id' => $teacherProfile1->id,
            'task_id' => $task1->id,
            'subject' => 'Mathématiques',
            'grade' => 15.50,
            'max_grade' => 20.00,
            'comments' => 'Bon travail, mais attention aux figures géométriques.',
            'graded_at' => now()->subDays(2),
        ]);

        Grade::create([
            'student_id' => $studentProfile1->id,
            'teacher_id' => $teacherProfile2->id,
            'task_id' => $task3->id,
            'subject' => 'Français',
            'grade' => 18.00,
            'max_grade' => 20.00,
            'comments' => 'Excellente compréhension du texte et bon résumé.',
            'graded_at' => now()->subDays(1),
        ]);

        Grade::create([
            'student_id' => $studentProfile2->id,
            'teacher_id' => $teacherProfile1->id,
            'task_id' => $task1->id,
            'subject' => 'Mathématiques',
            'grade' => 12.00,
            'max_grade' => 20.00,
            'comments' => 'Des erreurs dans les calculs, revoir les bases de l\'algèbre.',
            'graded_at' => now()->subDays(3),
        ]);

        Grade::create([
            'student_id' => $studentProfile2->id,
            'teacher_id' => $teacherProfile2->id,
            'task_id' => $task3->id,
            'subject' => 'Français',
            'grade' => 14.50,
            'max_grade' => 20.00,
            'comments' => 'Analyse correcte, mais manque de détails.',
            'graded_at' => now()->subDays(1),
        ]);

        Grade::create([
            'student_id' => $studentProfile3->id,
            'teacher_id' => $teacherProfile2->id,
            'task_id' => $task4->id,
            'subject' => 'Français',
            'grade' => 16.00,
            'max_grade' => 20.00,
            'comments' => 'Bon travail sur l\'analyse grammaticale.',
            'graded_at' => now()->subDays(2),
        ]);

    $this->command->info('✅ Données de test créées avec succès!');
    $this->command->info('');
    $this->command->info('📝 Comptes créés:');
    $this->command->info('Admin: admin@school.com / password123');
    $this->command->info('Professeur 1: dupont@school.com / password123');
    $this->command->info('Professeur 2: martin@school.com / password123');
    $this->command->info('Étudiant 1: marie.dubois@school.com / password123');
    $this->command->info('Étudiant 2: pierre.lambert@school.com / password123');
    $this->command->info('Étudiant 3: sophie.bernard@school.com / password123');
}
}

