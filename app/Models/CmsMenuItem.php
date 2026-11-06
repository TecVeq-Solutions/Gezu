<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsMenuItem extends Model
{
    use HasFactory;

    protected $table = 'cms_menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target',
        'order',
        'page_id',
    ];

    public function menu()
    {
        return $this->belongsTo(CmsMenu::class, 'menu_id');
    }

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }

    public function parent()
    {
        return $this->belongsTo(CmsMenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CmsMenuItem::class, 'parent_id')->orderBy('order', 'asc');
    }
}
