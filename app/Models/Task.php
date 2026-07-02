<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'type_id', 'status_id', 'priority',
        'due_date', 'assigned_admin_id', 'consumer_id', 'roadmap_item_id',
        'created_by_admin_id', 'created_by_consumer_id', 'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(TaskType::class);
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function roadmapItem()
    {
        return $this->belongsTo(RoadmapItem::class);
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }

    public function createdByConsumer()
    {
        return $this->belongsTo(Consumer::class, 'created_by_consumer_id');
    }
}
