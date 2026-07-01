<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSection extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'order', 'data'];

    protected $casts = [
        'data' => 'array',
    ];
}
