<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory;

    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'slug',
        'page_type',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'custom_schema',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getTypeNameAttribute()
    {
        return match($this->page_type) {
            'legal_terms' => 'Legal: Terms of Service',
            'legal_privacy' => 'Legal: Privacy Policy',
            'legal_clipper' => 'Legal: Clipper Agreement',
            'legal_brand' => 'Legal: Brand Guidelines',
            'about' => 'Company: About Us',
            'contact' => 'Company: Contact',
            default => 'Standard Page',
        };
    }
}
