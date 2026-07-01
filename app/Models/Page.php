<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'slug', 'nav_label', 'show_in_nav', 'nav_order',
        'parent_id', 'is_home', 'show_footer', 'is_published',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'show_in_nav'  => 'boolean',
        'is_home'      => 'boolean',
        'show_footer'  => 'boolean',
        'is_published' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('nav_order');
    }
}
