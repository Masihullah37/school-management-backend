<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'teacher_id',
        'task_id',
        'subject',
        'grade',
        'max_grade',
        'comments',
        'graded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'graded_at' => 'date',
            'grade' => 'decimal:2',
            'max_grade' => 'decimal:2',
        ];
    }

    /**
     * Relation avec Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relation avec Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relation avec Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}