<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tarjeta;

class Category extends Model
{
    protected $fillable = ['name'];

    public function tarjetas()
    {
        return $this->hasMany(Tarjeta::class);
    }
}
