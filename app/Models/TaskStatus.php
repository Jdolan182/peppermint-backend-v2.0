<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'color', 'sort_order', 'is_default', 'is_closed'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_closed'  => 'boolean',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'status_id');
    }
}
