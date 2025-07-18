<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'evaluation_type_id');
    }
} 