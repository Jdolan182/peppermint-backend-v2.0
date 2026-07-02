<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'color', 'icon', 'is_appointment', 'sort_order'];

    protected $casts = [
        'is_appointment' => 'boolean',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'type_id');
    }
}
