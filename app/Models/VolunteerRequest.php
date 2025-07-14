<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'motivation',
        'status',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
