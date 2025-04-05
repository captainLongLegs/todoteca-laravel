<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define the many.tomany relationship with the Book model.
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'user_books')
                    ->withPivot('status', 'rating', 'comment')
                    ->withTimestamps();
    }

    public function videogames()
    {
        return $this->belongsToMany(Videogame::class, 'user_videogame')
                    ->withPivot('status', 'playtime_hours', 'rating', 'comment')
                    ->withTimestamps();
    }
}
