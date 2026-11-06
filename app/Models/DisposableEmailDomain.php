<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposableEmailDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'is_blocked',
        'reason',
        'added_by',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
    ];
}
