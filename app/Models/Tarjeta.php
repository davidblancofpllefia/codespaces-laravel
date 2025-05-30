<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
class Tarjeta extends Model
{
    use HasFactory;

    protected $table = 'tarjetas';
      public $timestamps = false;

    protected $fillable = [
        'nombre',
        'imagen',
    ];

public function category()
{
    return $this->belongsTo(Category::class);
}


public function user()
{
    return $this->belongsTo(User::class);
}





}   
