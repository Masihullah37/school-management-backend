<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'roll_number',
        'class',
        'date_of_birth',
        'parent_phone',
        'address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Relation avec User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec Grades
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Obtenir les devoirs pour la classe de l'étudiant
     */
    public function tasks()
    {
        return Task::where('class', $this->class)->get();
    }
}
