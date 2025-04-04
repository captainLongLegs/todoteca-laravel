<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    public function videogames()
{
    return $this->belongsToMany(Videogame::class, 'game_platform');
}
}