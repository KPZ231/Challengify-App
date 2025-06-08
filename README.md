# Challengify

Challengify is a platform for daily micro-challenges in creativity, wellness, and skills, where users complete tasks, share responses, and earn badges through community voting.

## Setup

1. Clone the repository
2. Run `composer install` to install dependencies
3. Copy `.env.example` to `.env` and configure your environment variables
4. Run migrations: `vendor/bin/phinx migrate`
5. Run seeds: `vendor/bin/phinx seed:run`
6. Start the server: `php -S localhost:8000 -t public`

## Authentication and Security

The application implements a secure authentication system with the following features:

### Registration and Login

- `POST /api/register` - Register a new user
  - Required fields: username, email, password
  - Password is hashed using Bcrypt
  - Returns 201 Created on success

- `POST /api/login` - Login and receive JWT token
  - Required fields: email, password
  - Returns JWT token with 1-hour expiration
  - Protected with rate limiting (5 attempts per minute)

### JWT Authentication

- All protected endpoints require a valid JWT token
- Token must be sent in the Authorization header: `Authorization: Bearer {token}`
- Middleware validates token authenticity and expiration
- User data is passed to controllers for authorization checks

### Security Measures

- Input sanitization to prevent XSS attacks
- Prepared statements (PDO) for all database operations
- Rate limiting on sensitive endpoints (login, submissions)
- Password hashing with Bcrypt
- CSRF protection for web forms

## API Endpoints

### Authentication

- `POST /api/register` - Register a new user
- `POST /api/login` - Login and get JWT token

### Protected Endpoints

All API endpoints except for registration and login require JWT authentication.

## Environment Variables

Create a `.env` file in the root directory with the following variables:

```
APP_NAME=Challengify
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=UTC

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=challengify
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=your_jwt_secret_key_here
JWT_EXPIRATION=3600
```

## Security Best Practices

- Always use HTTPS in production
- Generate a strong random JWT secret key
- Keep dependencies updated
- Implement proper logging for security events
- Use input validation for all user inputs

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Web server with URL rewriting (Apache, Nginx, etc.)

## Project Structure

- `config/` - Configuration files
- `public/` - Web server root directory
- `src/` - Application source code
- `tests/` - Test files
- `vendor/` - Composer dependencies

## Development

- Run tests: `vendor/bin/phpunit`
- Check coding standards: `vendor/bin/phpcs`
- Fix coding standards: `vendor/bin/php-cs-fixer fix`
- Static analysis: `vendor/bin/phpstan analyse src tests`

## License

Proprietary - All rights reserved. 

