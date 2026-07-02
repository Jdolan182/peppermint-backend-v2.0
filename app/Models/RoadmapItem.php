<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'status', 'start_date', 'date', 'category_id',
        'assigned_admin_id', 'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'date'       => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(RoadmapCategory::class, 'category_id');
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
