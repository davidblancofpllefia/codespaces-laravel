<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mascota extends Model
{
    use HasFactory;

    protected $table = 'mascotas';

    protected $fillable = ['nombre', 'tipo', 'edad', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

