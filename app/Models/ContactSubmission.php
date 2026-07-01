<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactSubmission extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'email', 'message', 'page_slug', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }
}
