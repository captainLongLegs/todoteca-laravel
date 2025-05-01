<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'language',
        'pages',
        'genre',
        'cover_image',
        'description',
        'publisher',
        'publication_year_this_publisher',
        'publication_year_original',
        'valoration',
        'comments',
        'format',
        'tags'
    ];

    /**
     * Define the many-to-many relationship with the User model.
     */
    public function usersCollectionBook()
    {
        return $this->belongsToMany(User::class, 'user_books')
            ->withPivot('status', 'rating', 'comment')
            ->withTimestamps();
    }
}
