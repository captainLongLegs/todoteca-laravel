<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Book;
use Illuminate\Database\Seeder;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear the table
        Book::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Add test data
        Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'isbn' => '9780743273565',
            'language' => 'English',
            'pages' => 180,
            'genre' => 'Classic',
            'cover_image' => 'https://example.com/great-gatsby.jpg',
            'description' => 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.',
            'publisher' => 'Scribner',
            'publication_year_this_publisher' => 2004,
            'publication_year_original' => 1925,
            'valoration' => 5,
            'comments' => 'A timeless classic.',
            'format' => 'Paperback',
            'tags' => 'classic, fiction',
        ]);

        Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'isbn' => '9780451524935',
            'language' => 'English',
            'pages' => 328,
            'genre' => 'Dystopian',
            'cover_image' => 'https://example.com/1984.jpg',
            'description' => 'A dystopian novel set in a totalitarian society ruled by the Party.',
            'publisher' => 'Signet Classic',
            'publication_year_this_publisher' => 1950,
            'publication_year_original' => 1949,
            'valoration' => 4,
            'comments' => 'A chilling vision of the future.',
            'format' => 'Paperback',
            'tags' => 'dystopian, fiction',
        ]);
    }
}
