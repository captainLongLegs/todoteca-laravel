# Todoteca

[![GitHub license](https://img.shields.io/github/license/captainLongLegs/todoteca-laravel)](https://github.com/captainLongLegs/todoteca-laravel/blob/main/LICENSE)
[![GitHub stars](https://img.shields.io/github/stars/captainLongLegs/todoteca-laravel?style=social)](https://github.com/captainLongLegs/todoteca-laravel/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/captainLongLegs/todoteca-laravel?style=social)](https://github.com/captainLongLegs/todoteca-laravel/network/members)

## Project Description

Todoteca is a web application developed using the Laravel framework (PHP). Its primary purpose is to allow users to **catalog and manage their personal collections of books and videogames**.

Users can:
- Search for books and videogames using external APIs (Open Library and RAWG.io).
- Add found items (from API searches or manually) to their personal collection.
- View, filter, and sort items in their personal collection.
- View, search, and sort items in the global local database lists.
- Edit personal collection details for each item (status, rating, comment, playtime).

The project focuses on demonstrating core web application development skills including user authentication, database management (Eloquent ORM), API integration, routing, and building a responsive user interface.

## Features (MVP)

This version of Todoteca includes the following core functionalities:

-   **User Authentication:** Secure registration, login, and logout using Laravel Breeze.
-   **Book Management:**
    -   API Search (Open Library) and display of results.
    -   Add books from API search results to the local database and user collection.
    -   Manual addition of books to the local database.
    -   View, **search, and sort** the list of all books in the local database.
-   **Videogame Management:**
    -   API Search (RAWG.io) and display of results.
    -   Add videogames from API search results to the local database and user collection (includes fetching detailed info like developer).
    -   Manual addition of videogames to the local database.
    -   View, **search, and sort** the list of all videogames in the local database.
-   **Personal Collection:**
    -   View personal collections for both books (`/my-books`) and videogames (`/my-videogames`).
    -   **Filter** the videogame collection by Status, Rating, Platform, and Genre.
    -   Edit collection-specific details for items (Status, Rating, Comment, Playtime).
    -   Remove items from the personal collection.
-   **Basic Responsive Design:** The interface adapts to different screen sizes using Bootstrap 5.

## Technology Stack

-   **Backend:** Laravel 10.x (PHP 8.1+)
-   **Database:** MySQL 8.0+
-   **Frontend:** Blade Templates, HTML5, CSS3, Bootstrap 5, (Minimal JavaScript)
-   **API Clients:** Laravel HTTP Client (based on Guzzle)
-   **External APIs:** Open Library API (Books), RAWG.io API (Videogames)
-   **Authentication:** Laravel Breeze
-   **Version Control:** Git
-   **Repository:** GitHub

## Installation and Setup

Follow these steps to get the Todoteca project running on your local machine.

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/captainLongLegs/todoteca-laravel todoteca
    cd todoteca
    ```
    
2.  **Install PHP Dependencies:**
    Make sure you have Composer installed.
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies and Build Assets (Optional but Recommended):**
    Make sure you have Node.js and npm (or yarn) installed.
    ```bash
    npm install
    npm run dev
    ```
    *(Use `npm run build` for production assets)*

4.  **Set up Environment File:**
    Copy the example environment file and create your working `.env` file.
    ```bash
    cp .env.example .env
    ```
    Open the `.env` file and configure your database credentials and other settings.

5.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Database Setup:**
    Make sure your MySQL server is running and you have created a database (e.g., `todoteca`). Update your `.env` file with the database connection details.
    Run the migrations to create the necessary tables:
    ```bash
    php artisan migrate
    ```
    *(Optional: If seeders are provided for initial data, run `php artisan db:seed`)*

7.  **API Configuration (CRUCIAL for API Search Features):**
    The application relies on external APIs for searching books and videogames. You need to obtain API keys and configure them in your `.env` file.
    *   **Open Library API:** No API key is typically required for the basic search endpoint used.
    *   **RAWG.io API:** Obtain a free API key from [https://rawg.io/apidocs](https://rawg.io/apidocs). Add this key to your `.env` file:
        ```dotenv
        RAWG_API_KEY=YOUR_ACTUAL_RAWG_API_KEY
        VIDEOGAME_API_BASE_URL=https://api.rawg.io/api # Ensure this is correct
        ```
    *   **Note:** Without the correct RAWG.io API key configured, the videogame API search and the ability to add games from search results will not function. Network/ISP blocks (like those potentially in Spain related to football piracy) can also prevent API access even with a valid key.

8.  **Run the Application:**
    Start the Laravel development server:
    ```bash
    php artisan serve
    ```
    The application should be accessible at `http://127.0.0.1:8000` (or the URL displayed in your console).

## Full Project Documentation

A comprehensive documentation file detailing the project's initial study, design, execution (including technical implementation, database schema, security measures, and code structure), testing plan, test results, and evaluation is available in the repository:

-   **[PI_XavierMoralesReche.docx](PI_XavierMoralesReche.docx)**

This document provides in-depth information complementing the code in this repository.

## Author

Xavier Morales Reche
https://www.linkedin.com/in/xavier-morales-reche-719266115/

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). Your project code built on top of Laravel would typically also have a license; consider adding a `LICENSE` file (MIT is common for open source).