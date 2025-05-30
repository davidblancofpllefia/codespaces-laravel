<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['user_id', 'clicks', 'points', 'duration'];

    public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}


}
