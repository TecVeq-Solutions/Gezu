<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'user_id',
        'exception_type',
        'message',
        'file',
        'line',
        'url',
        'trace'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
