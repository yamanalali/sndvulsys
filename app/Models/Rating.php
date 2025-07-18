<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\EvaluationType;

class Rating extends Model
{
    protected $fillable = [
        'volunteer_id',
        'evaluation_type_id',
        'score',
        'date',
        'evaluator_id',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluationType()
    {
        return $this->belongsTo(EvaluationType::class, 'evaluation_type_id');
    }
} 