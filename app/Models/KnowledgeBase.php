<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = ['content', 'embedding'];

    protected $casts = [
        'embedding' => 'array',
    ];
}
