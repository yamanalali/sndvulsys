<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'evaluator_id',    // değerlendiren kullanıcı
        'volunteer_id',    // değerlendirilen gönüllü
        'type',            // değerlendirme türü
        'rating',          // puan
        'comment',         // yorum
    ];

    // değerlendirmeyi yapan kişi
    public function evaluator() {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // değerlendirilen gönüllü
    public function volunteer() {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
} 