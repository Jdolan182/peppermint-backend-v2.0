<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color', 'sort_order'];

    public function items()
    {
        return $this->hasMany(RoadmapItem::class, 'category_id');
    }
}
