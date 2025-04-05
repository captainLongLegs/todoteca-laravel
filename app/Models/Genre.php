<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Relationship: A genre can have many videogames.
     */
    public function videogames()
    {
        return $this->belongsToMany(Videogame::class, 'game_genre');
    }


}
