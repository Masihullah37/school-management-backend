<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec Student
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Relation avec Teacher
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Vérifier si l'utilisateur est un étudiant
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Vérifier si l'utilisateur est un professeur
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Vérifier si l'utilisateur est un administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    

  
}