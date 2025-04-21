<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videogame extends Model
{
    use HasFactory;
    protected $fillable = [
        'api_id',
        'slug',
        'name',
        'description',
        'released',
        'background_image',
        'age_rating',
        'developer',
        'publisher'
    ];

    protected $casts = [
        // Casts release_date to a Carbon date object
        'released' => 'datetime',
    ];

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'game_platform');
    }

    public function users()
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

