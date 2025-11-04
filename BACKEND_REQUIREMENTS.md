# Backend Requirements - Tinder-like Dating App

## 📋 Project Overview

This is a **senior developer technical assignment** for building the backend API of a Tinder-like dating application using PHP Laravel. The backend will serve a React Native mobile application with RESTful APIs for profile browsing, liking/disliking functionality, and administrative notifications.

### 🎯 Senior Developer Expectations
As a **6+ years experienced senior developer**, you are expected to demonstrate:
- **Design Patterns** implementation (Repository, Service, Factory, Strategy, Observer)
- **SOLID Principles** adherence
- **Clean Architecture** with proper separation of concerns
- **Scalable and Maintainable** codebase
- **Professional coding standards** and best practices
- **Enterprise-level** code organization

---

## 🎯 Assignment Objective

Build a **production-ready Laravel API** that handles:
- User profile management
- Recommendation engine (simple algorithm)
- Like/Dislike interactions
- Liked profiles listing
- Automated email notifications via cron job
- Complete API documentation (Swagger/OpenAPI)

### Architecture Goals
Implement using **industry-standard design patterns** and demonstrate:
- Clean, testable, and maintainable code
- Proper abstraction layers (Controller → Service → Repository → Model)
- Business logic separation from HTTP layer
- Dependency Injection and Interface-based programming
- SOLID principles throughout the codebase

---

## 🛠️ Technology Stack

### Required Technologies
- **Framework**: PHP Laravel 10.x
- **PHP Version**: 8.1 or higher
- **Database**: MySQL 8.0 / PostgreSQL 14+
- **Authentication**: Laravel Sanctum (optional, or simple token-based)
- **API Documentation**: L5-Swagger / Scramble
- **Task Scheduling**: Laravel Scheduler (Cron Jobs)
- **Email**: Laravel Mail (Mailtrap for testing / Log driver acceptable)

### Recommended Packages
```json
{
  "laravel/framework": "^10.0",
  "laravel/sanctum": "^3.0",
  "darkaonline/l5-swagger": "^8.5",
  "fakerphp/faker": "^1.23"
}
```

---

## 🏗️ Architecture & Design Patterns (Senior Developer Level)

### Required Design Patterns

#### 1. **Repository Pattern**
Abstracts data layer and database operations from business logic.

**Structure**:
```
app/
├── Repositories/
│   ├── Contracts/
│   │   ├── UserRepositoryInterface.php
│   │   ├── LikeRepositoryInterface.php
│   │   └── PhotoRepositoryInterface.php
│   ├── UserRepository.php
│   ├── LikeRepository.php
│   └── PhotoRepository.php
```

**Benefits**:
- Testability (easy to mock)
- Database agnostic
- Single source of truth for queries

**Example**:
```php
interface UserRepositoryInterface
{
    public function find(int $id): ?User;
    public function getRecommendedUsers(int $userId, array $filters): LengthAwarePaginator;
    public function create(array $data): User;
}

class UserRepository implements UserRepositoryInterface
{
    public function getRecommendedUsers(int $userId, array $filters): LengthAwarePaginator
    {
        // Complex query logic here
    }
}
```

---

#### 2. **Service Layer Pattern**
Contains business logic and orchestrates operations across multiple repositories.

**Structure**:
```
app/
├── Services/
│   ├── UserService.php
│   ├── MatchingService.php
│   ├── LikeService.php
│   └── NotificationService.php
```

**Benefits**:
- Thin controllers
- Reusable business logic
- Single Responsibility Principle

**Example**:
```php
class LikeService
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private UserRepositoryInterface $userRepository,
        private NotificationService $notificationService
    ) {}

    public function likeUser(int $likerId, int $likedId): array
    {
        // Business logic validation
        // Create like record
        // Check if user reached 50 likes
        // Trigger notification if needed
    }
}
```

---

#### 3. **Data Transfer Objects (DTOs)**
Structured data containers for API requests and responses.

**Structure**:
```
app/
├── DTOs/
│   ├── UserDTO.php
│   ├── RecommendedUserDTO.php
│   ├── LikeDTO.php
│   └── PaginationDTO.php
```

**Benefits**:
- Type safety
- Clear data contracts
- Easy serialization

**Example**:
```php
class RecommendedUserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $age,
        public readonly ?string $bio,
        public readonly string $location,
        public readonly ?float $distanceKm,
        public readonly array $photos
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'age' => $this->age,
            'bio' => $this->bio,
            'location' => $this->location,
            'distance_km' => $this->distanceKm,
            'photos' => $this->photos,
        ];
    }
}
```

---

#### 4. **Strategy Pattern**
Different algorithms for user recommendation.

**Structure**:
```
app/
├── Strategies/
│   ├── RecommendationStrategyInterface.php
│   ├── RandomRecommendationStrategy.php
│   ├── LocationBasedRecommendationStrategy.php
│   └── PopularityBasedRecommendationStrategy.php
```

**Benefits**:
- Easy to swap algorithms
- Open/Closed Principle
- Testable independently

**Example**:
```php
interface RecommendationStrategyInterface
{
    public function getRecommendedUsers(User $user, int $perPage): Collection;
}

class LocationBasedRecommendationStrategy implements RecommendationStrategyInterface
{
    public function getRecommendedUsers(User $user, int $perPage): Collection
    {
        // Distance-based algorithm
    }
}
```

---

#### 5. **Observer Pattern (Event-Driven)**
Handle side effects when actions occur (like reaching 50 likes).

**Structure**:
```
app/
├── Events/
│   ├── UserLikedEvent.php
│   └── UserReached50LikesEvent.php
├── Listeners/
│   └── SendAdminNotificationListener.php
```

**Benefits**:
- Loose coupling
- Easy to add new behaviors
- Asynchronous processing

**Example**:
```php
// Event
class UserReached50LikesEvent
{
    public function __construct(public User $user, public int $likeCount) {}
}

// Listener
class SendAdminNotificationListener
{
    public function handle(UserReached50LikesEvent $event): void
    {
        Mail::to(config('app.admin_email'))
            ->send(new UserPopularityNotification($event->user, $event->likeCount));
    }
}
```

---

#### 6. **Factory Pattern**
Create complex objects (users with photos, relationships).

**Structure**:
```
app/
├── Factories/
│   ├── UserFactory.php (Eloquent Factory)
│   └── ResponseFactory.php
```

**Example**:
```php
class ResponseFactory
{
    public static function success($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(string $message, $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
```

---

#### 7. **Dependency Injection**
Use Laravel's Service Container for loose coupling.

**Service Provider Registration**:
```php
// app/Providers/RepositoryServiceProvider.php
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        
        $this->app->bind(
            LikeRepositoryInterface::class,
            LikeRepository::class
        );
    }
}
```

**Controller Injection**:
```php
class PeopleController extends Controller
{
    public function __construct(
        private UserService $userService,
        private LikeService $likeService
    ) {}
}
```

---

### SOLID Principles Implementation

#### **S - Single Responsibility Principle**
- Controllers: Only handle HTTP requests/responses
- Services: Only contain business logic
- Repositories: Only handle data access
- Models: Only represent database entities

#### **O - Open/Closed Principle**
- Strategy pattern for recommendations (open for extension)
- Interface-based repositories (closed for modification)

#### **L - Liskov Substitution Principle**
- All recommendation strategies are interchangeable
- Repository implementations can be swapped

#### **I - Interface Segregation Principle**
- Small, focused interfaces (UserRepositoryInterface, LikeRepositoryInterface)
- No fat interfaces with unused methods

#### **D - Dependency Inversion Principle**
- Depend on abstractions (interfaces), not concrete classes
- All dependencies injected via constructor

---

### Project Structure (Clean Architecture)

```
tinder-api/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CheckPopularUsersCommand.php
│   ├── DTOs/
│   │   ├── UserDTO.php
│   │   ├── RecommendedUserDTO.php
│   │   └── PaginationDTO.php
│   ├── Events/
│   │   ├── UserLikedEvent.php
│   │   └── UserReached50LikesEvent.php
│   ├── Exceptions/
│   │   ├── UserNotFoundException.php
│   │   └── AlreadyLikedException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── PeopleController.php
│   │   ├── Requests/
│   │   │   ├── LoginRequest.php
│   │   │   └── RecommendedPeopleRequest.php
│   │   ├── Resources/
│   │   │   ├── UserResource.php
│   │   │   └── RecommendedUserResource.php
│   │   └── Middleware/
│   ├── Listeners/
│   │   └── SendAdminNotificationListener.php
│   ├── Mail/
│   │   └── UserPopularityNotification.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Photo.php
│   │   ├── Like.php
│   │   ├── Dislike.php
│   │   └── AdminNotification.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── LikeRepositoryInterface.php
│   │   │   ├── DislikeRepositoryInterface.php
│   │   │   └── PhotoRepositoryInterface.php
│   │   ├── UserRepository.php
│   │   ├── LikeRepository.php
│   │   ├── DislikeRepository.php
│   │   └── PhotoRepository.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── UserService.php
│   │   ├── LikeService.php
│   │   ├── DislikeService.php
│   │   ├── MatchingService.php
│   │   └── NotificationService.php
│   ├── Strategies/
│   │   ├── RecommendationStrategyInterface.php
│   │   ├── RandomRecommendationStrategy.php
│   │   └── LocationBasedRecommendationStrategy.php
│   └── Providers/
│       ├── RepositoryServiceProvider.php
│       └── EventServiceProvider.php
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── PhotoFactory.php
│   ├── migrations/
│   │   ├── 2024_01_01_create_users_table.php
│   │   ├── 2024_01_02_create_photos_table.php
│   │   ├── 2024_01_03_create_likes_table.php
│   │   ├── 2024_01_04_create_dislikes_table.php
│   │   └── 2024_01_05_create_admin_notifications_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── UserSeeder.php
├── routes/
│   └── api.php
└── tests/
    ├── Feature/
    │   ├── AuthTest.php
    │   ├── RecommendedPeopleTest.php
    │   ├── LikeTest.php
    │   └── DislikeTest.php
    └── Unit/
        ├── Services/
        │   ├── LikeServiceTest.php
        │   └── UserServiceTest.php
        └── Repositories/
            └── UserRepositoryTest.php
```

---

### Code Quality Standards

1. **PSR-12** coding standard compliance
2. **Type declarations** for all methods (PHP 8.1+)
3. **PHPDoc blocks** for complex methods
4. **Meaningful names** (no abbreviations)
5. **DRY principle** (Don't Repeat Yourself)
6. **KISS principle** (Keep It Simple, Stupid)
7. **YAGNI principle** (You Aren't Gonna Need It)

**Example**:
```php
class LikeService
{
    /**
     * Process a like action from one user to another.
     *
     * @throws UserNotFoundException
     * @throws AlreadyLikedException
     */
    public function likeUser(int $likerId, int $likedId): LikeDTO
    {
        $this->validateLike($likerId, $likedId);
        
        $like = $this->likeRepository->create([
            'liker_id' => $likerId,
            'liked_id' => $likedId,
        ]);
        
        $this->checkAndNotifyIfPopular($likedId);
        
        event(new UserLikedEvent($likerId, $likedId));
        
        return LikeDTO::fromModel($like);
    }
}
```

---

## 📊 Database Schema

### Tables & Relationships

#### 1. **users** table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    age INT UNSIGNED NOT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT 'other',
    bio TEXT NULL,
    location VARCHAR(255) NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_is_active (is_active),
    INDEX idx_location (latitude, longitude)
);
```

#### 2. **photos** table
```sql
CREATE TABLE photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    url VARCHAR(512) NOT NULL,
    display_order TINYINT UNSIGNED DEFAULT 1,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_primary (user_id, is_primary)
);
```

#### 3. **likes** table
```sql
CREATE TABLE likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liker_id BIGINT UNSIGNED NOT NULL COMMENT 'User who liked',
    liked_id BIGINT UNSIGNED NOT NULL COMMENT 'User who was liked',
    created_at TIMESTAMP NULL,
    FOREIGN KEY (liker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (liked_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (liker_id, liked_id),
    INDEX idx_liker (liker_id),
    INDEX idx_liked (liked_id),
    INDEX idx_created_at (created_at)
);
```

#### 4. **dislikes** table
```sql
CREATE TABLE dislikes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'User who disliked',
    disliked_id BIGINT UNSIGNED NOT NULL COMMENT 'User who was disliked',
    created_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (disliked_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_dislike (user_id, disliked_id),
    INDEX idx_user (user_id),
    INDEX idx_disliked (disliked_id)
);
```

#### 5. **admin_notifications** table
```sql
CREATE TABLE admin_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'User who received 50+ likes',
    like_count INT UNSIGNED NOT NULL COMMENT 'Snapshot of like count',
    email_sent_to VARCHAR(255) NOT NULL,
    email_sent_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_email_sent_at (email_sent_at)
);
```

### Entity Relationship Diagram

```
users (1) ─────< (N) photos
  │
  │ (liker)
  ├─────< (N) likes >─────┤ (liked)
  │                       │
  │ (user)                │
  ├─────< (N) dislikes >──┤ (disliked)
  │                       │
  └─────< (N) admin_notifications
```

---

## 🔌 API Endpoints

### Base URL
```
https://your-domain.com/api
```

### Authentication (Optional but Recommended)
```
POST   /api/auth/register     - Register new user
POST   /api/auth/login        - Login (returns token)
POST   /api/auth/logout       - Logout
GET    /api/auth/me           - Get authenticated user
```

---

### 1. **GET /api/people/recommended**

**Description**: Get a paginated list of recommended people for the authenticated user.

**Authentication**: Required (Bearer token or session)

**Query Parameters**:
| Parameter | Type    | Required | Default | Description                |
|-----------|---------|----------|---------|----------------------------|
| page      | integer | No       | 1       | Current page number        |
| per_page  | integer | No       | 20      | Items per page (max: 100)  |
| gender    | string  | No       | null    | Filter by gender           |
| min_age   | integer | No       | 18      | Minimum age filter         |
| max_age   | integer | No       | 100     | Maximum age filter         |

**Logic**:
- Exclude users already liked by the authenticated user
- Exclude users already disliked by the authenticated user
- Exclude the authenticated user themselves
- Order by: Random or created_at DESC (your choice)
- Calculate distance from authenticated user (if location available)

**Success Response (200 OK)**:
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "name": "Esther Kim",
      "age": 30,
      "bio": "Love traveling and coffee ☕",
      "location": "Seoul, South Korea",
      "distance_km": 24,
      "photos": [
        {
          "id": 1,
          "url": "https://storage.example.com/photos/user123_1.jpg",
          "display_order": 1,
          "is_primary": true
        },
        {
          "id": 2,
          "url": "https://storage.example.com/photos/user123_2.jpg",
          "display_order": 2,
          "is_primary": false
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8
  }
}
```

**Error Responses**:
```json
// 401 Unauthorized
{
  "success": false,
  "message": "Unauthenticated"
}

// 422 Validation Error
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "per_page": ["The per page must not be greater than 100."]
  }
}
```

---

### 2. **POST /api/people/{id}/like**

**Description**: Like a specific person/profile.

**Authentication**: Required

**URL Parameters**:
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | User ID to like       |

**Request Body**: None (or empty JSON object)

**Logic**:
- Check if user exists and is not already liked
- Check if user is not the authenticated user (can't like yourself)
- Create a new like record
- Remove dislike record if exists (user changed their mind)
- Return success response

**Success Response (200 OK)**:
```json
{
  "success": true,
  "message": "Successfully liked user",
  "data": {
    "liked_user_id": 123,
    "liked_at": "2025-11-04T12:45:30Z"
  }
}
```

**Error Responses**:
```json
// 404 Not Found
{
  "success": false,
  "message": "User not found"
}

// 400 Bad Request
{
  "success": false,
  "message": "You cannot like yourself"
}

// 409 Conflict
{
  "success": false,
  "message": "You have already liked this user"
}
```

---

### 3. **POST /api/people/{id}/dislike**

**Description**: Dislike/pass on a specific person/profile.

**Authentication**: Required

**URL Parameters**:
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| id        | integer | Yes      | User ID to dislike    |

**Request Body**: None

**Logic**:
- Check if user exists
- Check if user is not the authenticated user
- Create a new dislike record
- Remove like record if exists (user changed their mind)
- Return success response

**Success Response (200 OK)**:
```json
{
  "success": true,
  "message": "Successfully disliked user",
  "data": {
    "disliked_user_id": 123,
    "disliked_at": "2025-11-04T12:46:15Z"
  }
}
```

**Error Responses**: Same as like endpoint

---

### 4. **GET /api/people/liked**

**Description**: Get a paginated list of people the authenticated user has liked.

**Authentication**: Required

**Query Parameters**:
| Parameter | Type    | Required | Default | Description          |
|-----------|---------|----------|---------|----------------------|
| page      | integer | No       | 1       | Current page number  |
| per_page  | integer | No       | 20      | Items per page       |

**Success Response (200 OK)**:
```json
{
  "success": true,
  "data": [
    {
      "id": 456,
      "name": "John Doe",
      "age": 28,
      "bio": "Software engineer",
      "location": "Busan, South Korea",
      "distance_km": 35,
      "liked_at": "2025-11-03T10:30:00Z",
      "photos": [
        {
          "id": 10,
          "url": "https://storage.example.com/photos/user456_1.jpg",
          "display_order": 1,
          "is_primary": true
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 45,
    "last_page": 3
  }
}
```

---

## ⏰ Cron Job Requirement

### Feature: Admin Notification on 50+ Likes

**Objective**: Automatically send an email to the admin when any user receives 50 or more likes.

**Implementation Requirements**:

1. **Laravel Command**:
```php
php artisan app:check-popular-users
```

2. **Schedule**: Run every hour (or configurable)
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('app:check-popular-users')
             ->hourly()
             ->withoutOverlapping();
}
```

3. **Logic**:
   - Query users with 50+ likes who haven't been notified yet
   - Send email to admin (e.g., admin@example.com)
   - Store notification record in `admin_notifications` table
   - Prevent duplicate notifications

4. **Email Content**:
```
Subject: User Has Received 50+ Likes

Hello Admin,

User [User Name] (ID: [User ID]) has received 50 likes!

Profile Details:
- Name: [Name]
- Age: [Age]
- Location: [Location]
- Total Likes: [Count]
- Profile Link: https://app.example.com/users/[ID]

This is an automated notification.
```

5. **Testing Command**:
```bash
php artisan app:check-popular-users
```

---

## 📚 Swagger/OpenAPI Documentation

### Requirements

1. **Auto-generate** API documentation from code annotations
2. **Interactive UI** at `/api/documentation` endpoint
3. **Include**:
   - All endpoints with descriptions
   - Request/response schemas
   - Authentication methods
   - Example requests/responses
   - Error codes

### Example Swagger Annotation
```php
/**
 * @OA\Get(
 *     path="/api/people/recommended",
 *     summary="Get recommended people",
 *     tags={"People"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/RecommendedPeopleResponse")
 *     )
 * )
 */
```

---

## 🚀 Deployment Requirements

### 1. **Environment Setup**
```env
APP_NAME="Tinder API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-api-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinder_db
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
ADMIN_EMAIL=admin@example.com
```

### 2. **Hosting Requirements**
- PHP 8.1+ with required extensions
- MySQL 8.0 or PostgreSQL
- Composer installed
- Cron job support (for Laravel Scheduler)
- HTTPS enabled (SSL certificate)

### 3. **Deployment Checklist**
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed database: `php artisan db:seed`
- [ ] Generate Swagger docs: `php artisan l5-swagger:generate`
- [ ] Setup cron job:
  ```bash
  * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] Optimize application:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
- [ ] Test all endpoints via Swagger UI

### 4. **Public Access**
- Swagger documentation must be accessible at: `https://your-domain.com/api/documentation`
- All API endpoints must be testable from external networks

---

## 🧪 Testing Requirements

### Seed Data
Create database seeders with:
- **100 fake users** with diverse profiles
- **3-5 photos per user**
- **Random likes/dislikes** between users
- At least **2-3 users with 50+ likes** (to test cron job)

### Manual Testing Checklist
- [ ] Register/Login works
- [ ] GET recommended people returns results
- [ ] POST like creates record correctly
- [ ] POST dislike creates record correctly
- [ ] GET liked people returns correct list
- [ ] Cron job sends email when user reaches 50 likes
- [ ] Swagger UI loads and all endpoints are documented
- [ ] Pagination works correctly
- [ ] Authentication protects endpoints

---

## 📦 Deliverables

### 1. **Source Code**
- GitHub repository with clean commit history
- `.env.example` file with all required variables
- Complete database migrations
- Seeders with fake data

### 2. **Documentation**
- `README.md` with:
  - Installation instructions
  - Environment setup
  - Running migrations and seeders
  - Cron job setup
  - API usage examples
- Database schema diagram (ERD)

### 3. **Deployed Application**
- Live API URL: `https://your-domain.com`
- Swagger documentation URL: `https://your-domain.com/api/documentation`
- Admin email for testing cron notifications

### 4. **Testing Credentials**
Provide test credentials:
```
Email: test@example.com
Password: password123
```

---

## 🎯 Evaluation Criteria (Senior Developer Level)

Your submission will be evaluated on:

1. **Architecture & Design Patterns** (35%) ⭐ **CRITICAL**
   - Proper implementation of Repository Pattern
   - Service Layer separation with clear business logic
   - Use of DTOs for data transfer
   - Strategy Pattern for recommendations (if applicable)
   - Event-driven architecture (Observer pattern)
   - SOLID principles adherence throughout codebase
   - Dependency Injection and Interface usage
   - Clean Architecture layers (Controller → Service → Repository → Model)

2. **Code Quality & Standards** (25%)
   - Clean, readable, and maintainable code
   - Proper use of Laravel conventions and best practices
   - PSR-12 coding standard compliance
   - Type hints and return types (PHP 8.1+)
   - Meaningful variable/method names
   - Proper error handling with custom exceptions
   - No code duplication (DRY principle)
   - KISS and YAGNI principles
   - PHPDoc blocks for complex methods

3. **Database Design** (15%)
   - Efficient schema design
   - Proper relationships and indexes
   - Data integrity (foreign keys, constraints)
   - Query optimization
   - Eloquent relationships properly defined

4. **API Implementation** (15%)
   - RESTful design principles
   - Proper HTTP status codes
   - Consistent response format
   - Input validation using Form Requests
   - Thin controllers (all business logic in services)
   - Resource classes for responses
   - Proper error handling middleware

5. **Documentation & Testing** (10%)
   - Complete Swagger/OpenAPI documentation
   - Clear README with architecture explanation
   - Architecture diagram (optional but impressive)
   - Unit tests for services and repositories
   - Feature tests for API endpoints
   - Code comments where needed (not obvious code)

---

## 🧪 Testing Requirements (Strongly Recommended)

### Unit Tests
Create tests for your services and repositories to demonstrate professional testing practices:

```
tests/
├── Unit/
│   ├── Services/
│   │   ├── LikeServiceTest.php
│   │   ├── UserServiceTest.php
│   │   └── MatchingServiceTest.php
│   └── Repositories/
│       ├── UserRepositoryTest.php
│       └── LikeRepositoryTest.php
```

### Feature Tests
Test all API endpoints:

```
tests/
├── Feature/
│   ├── AuthTest.php
│   ├── RecommendedPeopleTest.php
│   ├── LikeUserTest.php
│   ├── DislikeUserTest.php
│   └── LikedPeopleListTest.php
```

### Testing Best Practices
- Mock dependencies in unit tests
- Use database transactions for feature tests
- Test happy paths and edge cases
- Test validation rules
- Test authentication/authorization

**Run tests**:
```bash
php artisan test
php artisan test --coverage  # If you have Xdebug/PCOV
```

### Example Test Structure
```php
class LikeServiceTest extends TestCase
{
    use RefreshDatabase;

    private LikeService $likeService;
    private LikeRepositoryInterface $likeRepository;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->likeRepository = Mockery::mock(LikeRepositoryInterface::class);
        $this->likeService = new LikeService($this->likeRepository, ...);
    }
    
    public function test_user_can_like_another_user(): void
    {
        // Arrange
        $likerId = 1;
        $likedId = 2;
        
        // Act
        $result = $this->likeService->likeUser($likerId, $likedId);
        
        // Assert
        $this->assertInstanceOf(LikeDTO::class, $result);
    }
}
```

---

## 📝 Implementation Notes

### Recommended People Algorithm (Simple)
```php
// Exclude already liked/disliked users
User::whereNotIn('id', $likedUserIds)
    ->whereNotIn('id', $dislikedUserIds)
    ->where('id', '!=', auth()->id())
    ->where('is_active', true)
    ->inRandomOrder() // or orderBy('created_at', 'desc')
    ->paginate(20);
```

### Distance Calculation (Haversine Formula)
```php
// Calculate distance between two points (lat/lng)
$distance = DB::raw(
    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
    cos(radians(longitude) - radians(?)) + 
    sin(radians(?)) * sin(radians(latitude))))"
);
```

### Cron Job Query
```php
// Find users with 50+ likes who haven't been notified
User::withCount('likesReceived')
    ->having('likes_received_count', '>=', 50)
    ->whereDoesntHave('adminNotifications')
    ->get();
```

---

## 🔒 Security Considerations

1. **Authentication**: Use Laravel Sanctum for API token authentication
2. **Rate Limiting**: Apply rate limits to prevent abuse
3. **Input Validation**: Validate all incoming requests
4. **SQL Injection**: Use Eloquent ORM / Query Builder (avoid raw queries)
5. **CORS**: Configure proper CORS headers for mobile app
6. **Environment Variables**: Never commit `.env` file

---

## 📞 Support

For questions or clarifications during implementation:
- Review Laravel documentation: https://laravel.com/docs
- Swagger documentation: https://github.com/DarkaOnLine/L5-Swagger

---

## ✅ Final Checklist (Senior Developer)

Before submission:

### Functionality
- [ ] All 4 API endpoints implemented and working
- [ ] Cron job working and tested
- [ ] Database properly seeded with fake data (100+ users)
- [ ] Authentication implemented (Sanctum/JWT)

### Architecture & Design Patterns
- [ ] Repository Pattern implemented for all models
- [ ] Service Layer created with business logic
- [ ] DTOs used for data transfer
- [ ] Event-driven architecture for notifications
- [ ] Interfaces created and bound in Service Provider
- [ ] Dependency Injection used throughout
- [ ] SOLID principles followed

### Code Quality
- [ ] PSR-12 coding standards compliant
- [ ] Type hints and return types on all methods
- [ ] Custom exceptions for error handling
- [ ] Form Requests for validation
- [ ] Resource classes for API responses
- [ ] No code duplication

### Testing
- [ ] Unit tests for services written
- [ ] Unit tests for repositories written
- [ ] Feature tests for all endpoints
- [ ] Tests passing: `php artisan test`

### Documentation
- [ ] Swagger documentation complete and accessible
- [ ] README with architecture explanation
- [ ] Setup instructions clear
- [ ] Architecture diagram (optional but recommended)
- [ ] .env.example provided

### Deployment
- [ ] Application deployed and accessible
- [ ] Database migrations run
- [ ] Seeders executed
- [ ] Cron job scheduled
- [ ] Code pushed to GitHub with clean commits

### Professional Touch
- [ ] Proper commit messages
- [ ] Clean Git history
- [ ] No commented-out code
- [ ] No debug statements left
- [ ] Proper .gitignore file

---

**Good luck with your senior-level implementation!** 🚀

**Remember**: Quality over quantity. Clean, well-architected code with proper design patterns will score higher than feature-rich but messy code.

