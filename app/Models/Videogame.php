<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videogame extends Model
{
    protected $fillable = [
        'title',
        'description',
        'release_date',
        'cover_image_url',
        'age_rating',
        'developer',
        'publisher'
    ];

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'game_platform');
    }

    public function usersCollectionVideogame()
    {
        return $this->belongsToMany(User::class, 'user_videogame')
            ->withPivot('status', 'playtime_hours', 'rating', 'comment')
            ->withTimestamps();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'game_genre');
    }
}

