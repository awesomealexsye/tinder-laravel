# Tinder-like Dating App Backend API

A production-ready Laravel 12 API built with clean architecture, implementing design patterns and SOLID principles for a Tinder-like dating application.

## 🎯 Features

- User authentication with Laravel Sanctum (token-based)
- Profile management with photos
- Recommendation engine for discovering users
- Like/Dislike functionality
- View liked users
- Automated admin notifications for popular users (50+ likes)
- Comprehensive API documentation with L5-Swagger
- Clean Architecture with Repository and Service patterns

## 🏗️ Architecture

This project follows **Clean Architecture** principles with strict separation of concerns:

### Design Patterns Implemented

- **Repository Pattern**: Abstracts data layer from business logic
- **Service Layer Pattern**: Contains business logic and orchestrates operations
- **Strategy Pattern**: Flexible recommendation algorithms
- **Observer Pattern**: Event-driven architecture for notifications
- **Factory Pattern**: Object creation and API response formatting
- **Dependency Injection**: Interface-based programming throughout

### Project Structure

```
app/
├── Console/Commands/      # Artisan commands (cron jobs)
├── Events/                # Application events
├── Exceptions/            # Custom exceptions
├── Factories/             # Response and object factories
├── Http/
│   ├── Controllers/       # Thin controllers (HTTP layer only)
│   ├── Requests/          # Form request validation
│   └── Resources/         # API response transformers
├── Listeners/             # Event listeners
├── Mail/                  # Email mailables
├── Models/                # Eloquent models
├── Repositories/          # Data access layer
│   └── Contracts/         # Repository interfaces
├── Services/              # Business logic layer
└── Strategies/            # Algorithm strategies
```

## 🛠️ Technology Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.3+
- **Database**: MySQL 8.0+ (configured)
- **Authentication**: Laravel Sanctum 4.x
- **API Documentation**: L5-Swagger 9.x
- **Testing**: PHPUnit

## 📋 Prerequisites

- PHP 8.3 or higher
- Composer
- MySQL 8.0+
- Node.js & NPM (for frontend assets)

## 🚀 Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd tinder-app/backend
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure your database

Update `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinder_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Configure mail settings (for notifications)

For development, you can use Mailtrap or Log driver:

```env
MAIL_MAILER=log
# Or for Mailtrap:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password

ADMIN_EMAIL=admin@example.com
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Seed the database

```bash
php artisan db:seed
```

This will create:
- 1 test user (email: test@example.com, password: password)
- 100 random users with photos
- Random likes and dislikes
- 2-3 users with 50+ likes (for testing notifications)

## 🎮 Running the Application

### Start the development server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### API Documentation

Access Swagger UI at: `http://localhost:8000/api/documentation`

Generate/regenerate documentation:

```bash
php artisan l5-swagger:generate
```

## 📡 API Endpoints

### Authentication (Public)

- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login and get token

### Authentication (Protected)

- `POST /api/auth/logout` - Logout (revoke token)
- `GET /api/auth/me` - Get authenticated user profile

### People (Protected - requires Bearer token)

- `GET /api/people/recommended` - Get recommended users
  - Query params: `page`, `per_page`, `gender`, `min_age`, `max_age`
- `POST /api/people/{id}/like` - Like a user
- `POST /api/people/{id}/dislike` - Dislike a user
- `GET /api/people/liked` - Get users you've liked

### Example Request

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "age": 28,
    "gender": "male"
  }'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'

# Get recommendations (with token)
curl -X GET "http://localhost:8000/api/people/recommended?per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## ⏰ Scheduled Tasks (Cron Job)

The application includes a scheduled command to check for popular users and notify admins.

### Manual execution

```bash
# Run the command
php artisan app:check-popular-users

# Dry run (test without sending emails)
php artisan app:check-popular-users --dry-run
```

### Setup Laravel Scheduler

Add this to your crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

The command runs hourly and checks for users with 50+ likes who haven't been notified yet.

## 🧪 Testing

### Run all tests

```bash
php artisan test
```

### Run specific test suites

```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Test Coverage

Feature tests cover:
- User registration and authentication
- Like/dislike functionality
- Recommendations API
- Authorization and validation

## 📦 Database Schema

### Users Table
- Basic profile: name, email, password
- Dating info: age, gender, bio, location
- Geolocation: latitude, longitude
- Status: is_active

### Photos Table
- Multiple photos per user
- Display order and primary photo flag

### Likes Table
- Track who liked whom
- Unique constraint prevents duplicates
- Timestamps for analytics

### Dislikes Table
- Track rejected profiles
- Prevents showing same user again

### Admin Notifications Table
- Logs when admins are notified
- Prevents duplicate notifications

## 🔒 Security Features

- Token-based authentication (Laravel Sanctum)
- Password hashing with bcrypt
- Request validation with Form Requests
- CORS configuration ready
- SQL injection protection (Eloquent ORM)
- Rate limiting on API routes

## 🎨 Code Quality

- **PSR-12** coding standard
- Type hints on all methods
- Comprehensive PHPDoc blocks
- SOLID principles adherence
- DRY, KISS, YAGNI principles

### Run code formatter

```bash
./vendor/bin/pint
```

## 📝 Testing Credentials

```
Email: test@example.com
Password: password
```

## 🐛 Troubleshooting

### Clear caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Regenerate autoload files

```bash
composer dump-autoload
```

### Database issues

```bash
php artisan migrate:fresh --seed
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum)
- [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)

## 👨‍💻 Architecture Highlights

### SOLID Principles

- **S**ingle Responsibility: Each class has one job
- **O**pen/Closed: Strategy pattern allows extension
- **L**iskov Substitution: Interface implementations are interchangeable
- **I**nterface Segregation: Small, focused interfaces
- **D**ependency Inversion: Depend on abstractions, not concretions

### Clean Code Practices

- Thin controllers (only HTTP logic)
- Business logic in services
- Data access in repositories
- Clear separation of concerns
- Testable and maintainable code

## 🤝 Contributing

This is a technical assignment project demonstrating senior-level PHP/Laravel development skills.

## 📄 License

This project is created for educational/assessment purposes.
