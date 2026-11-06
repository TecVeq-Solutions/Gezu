<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsMenu extends Model
{
    use HasFactory;

    protected $table = 'cms_menus';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function items()
    {
        return $this->hasMany(CmsMenuItem::class, 'menu_id')->orderBy('order', 'asc');
    }
}
