<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsCategory extends Model
{
    use HasFactory;

    protected $table = 'cms_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(CmsPost::class, 'category_id');
    }
}
