## Requirements

- PHP 
- HTTP server with PHP support (e.g.: Apache, Nginx, Caddy)
- [Composer](https://getcomposer.org/)
- MySQL database
- [npm](https://nodejs.org) (npm is installed with Node.js)

## Installation

Clone the repository.

Create MySQL database and call it covid19

Open the terminal and switch to the repo folder. Install all the dependencies using composer

    composer install

Run the database migrations

    php artisan migrate

Start the local server

    php artisan serve

The api can be accessed at [http://localhost:8000/api](http://localhost:8000/api).


Open another terminal and Install all the dependencies for the front end using npm

    npm install
    
Serve on http://localhost:8000

    npm run watch
    
