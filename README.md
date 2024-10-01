# Email Parser and API

This Laravel application parses raw email content and provides a RESTful API for managing successful emails.

## Requirements

- PHP 8.2+
- Composer
- MySQL

## Setup

1. Clone the repository:
   ```
   git clone https://github.com/Uziel79/inflektion-assessment
   cd inflektion-assessment
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Copy `.env.example` to `.env` and configure your database settings:
   ```
   cp .env.example .env
   ```

4. Generate application key:
   ```
   php artisan key:generate
   ```

5. Run migrations:
   ```
   php artisan migrate
   ```

6. (Optional) Seed the database with sample data:
   ```
   php artisan db:seed
   ```

## Usage

### API Endpoints

The following API endpoints are available:

- GET /api/successful-emails: List all successful emails (paginated)
- POST /api/successful-emails: Create a new successful email
- GET /api/successful-emails/{id}: Get a specific successful email
- PUT /api/successful-emails/{id}: Update a specific successful email
- DELETE /api/successful-emails/{id}: Delete a specific successful email

All endpoints are protected by Laravel Sanctum authentication.

### Email Parsing Command

To parse emails and extract plain text content:
    ```
    php artisan emails:parse
    ```

This command is scheduled to run hourly. To set up the scheduler, add the following Cron entry to your server:
    ```
    cd /var/www/inflektion-assessment && php artisan schedule:run >> /dev/null 2>&1
    ```

## Authentication

This API uses Laravel Sanctum for authentication. To authenticate:

1. Create a user (if not already created):
   ```
   php artisan tinker
   User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('password')]);
   ```

2. Obtain an API token by making a POST request to `/api/login` with the user's credentials.

3. Include the token in the `Authorization` header of your API requests:
   ```
   Authorization: Bearer <your-token>
   ```

## Testing

Run the test suite with:
    ```
    php artisan test
    ```


## License

This project is open-sourced software licensed under the MIT license.
