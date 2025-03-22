<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
    use HasFactory;

    //Specify the table name (optional, since it follows Laravel's naming convention)
    protected $table = 'user_books';

    /**
     * Define the relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the Book model.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
