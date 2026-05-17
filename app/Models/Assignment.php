<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'difficulty',
        'deadline',
        'completed'
    ];
}
