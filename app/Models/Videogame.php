<?php

namespace App\Models;

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

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status', 'playtime_hours', 'rating', 'comment')
            ->withTimestamps();
    }
}

