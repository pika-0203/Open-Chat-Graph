# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

オプチャグラフ (OpenChat Graph) is a web application that tracks and displays growth trends for LINE OpenChat communities. It crawls the official LINE OpenChat site hourly to collect member statistics and displays rankings, search functionality, and growth analytics.

- **Live Site**: https://openchat-review.me
- **Language**: Primarily Japanese
- **License**: MIT

## Development Environment

### Docker Setup
```bash
# Start development environment (PHP 8.3 + MySQL + phpMyAdmin)
docker-compose up

# Default ports:
# - Web: http://localhost:8000
# - MySQL: localhost:3306
# - phpMyAdmin: http://localhost:8080
```

### Initial Setup
```bash
# Install PHP dependencies and setup local config
composer install
./local-setup.sh
```

## Architecture

### Backend
- **Framework**: Custom MimimalCMS (lightweight MVC framework by project author)
- **Language**: PHP 8.3
- **Database**: MySQL/MariaDB for main data, SQLite for rankings/statistics
- **Architecture**: Traditional MVC with dependency injection

### Frontend
- **Hybrid approach**: Server-side PHP templating + embedded React components
- **Languages**: TypeScript, JavaScript, React
- **Libraries**: MUI, Chart.js, Swiper.js

### Key Directories
- `/app/` - Main application (MVC structure)
  - `Config/` - Routing and application config
  - `Controllers/` - HTTP handlers (Api/ and Page/ subdirs)
  - `Models/` - Data access layer with repositories
  - `Services/` - Business logic
  - `Views/` - Templates and React components
- `/shadow/` - Custom MimimalCMS framework
- `/batch/` - Background processing, cron jobs
  - `cron/` - Scheduled tasks
  - `exec/` - CLI executables  
  - `sh/` - Shell scripts for deployment/backup
- `/shared/` - Framework configuration and DI mappings
- `/storage/` - Multi-language data files, SQLite databases

## Database Architecture

### MySQL/MariaDB
- Primary storage for OpenChat data
- Complex queries using raw SQL (no ORM)
- Foreign key relationships with manual optimization

### SQLite
- Rankings and statistics data
- Performance optimization for read-heavy operations
- Separate databases per data type in `/storage/`

### Key Patterns
- Repository pattern with interfaces
- Dependency injection via `/shared/MimimalCmsConfig.php`
- Raw SQL for complex queries and performance

### Database Access in Controllers

When you need to access the database in controllers, use the `Shadow\DB` class:

```php
use Shadow\DB;

// In your controller method:
DB::connect(); // Always connect first

// For SELECT queries that return multiple rows:
$stmt = DB::$pdo->prepare("SELECT * FROM table WHERE condition = ?");
$stmt->execute([$value]);
$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// For SELECT queries that return a single row:
$stmt = DB::$pdo->prepare("SELECT * FROM table WHERE id = ?");
$stmt->execute([$id]);
$result = $stmt->fetch(\PDO::FETCH_ASSOC);

// For INSERT/UPDATE/DELETE:
$stmt = DB::$pdo->prepare("INSERT INTO table (column1, column2) VALUES (?, ?)");
$stmt->execute([$value1, $value2]);
```

Note: The database configuration is automatically loaded from `local-secrets.php` for development environment.

## Database Schema Reference

### MySQL Database (Primary Data)

#### Core Tables

**open_chat** - Main OpenChat data
```sql
CREATE TABLE `open_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `member` int(11) NOT NULL,
  `img_url` text NOT NULL,
  `url` text NOT NULL,
  `category` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `local_img_url` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`(191)),
  KEY `category` (`category`),
  KEY `member` (`member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**statistics_ranking_hour** - Hourly growth statistics (NO created_at column)
```sql
CREATE TABLE `statistics_ranking_hour` (
  `id` int(11) NOT NULL,
  `open_chat_id` int(11) NOT NULL,
  `diff_member` int(11) NOT NULL,
  `percent_increase` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `open_chat_id` (`open_chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**statistics_ranking_hour24** - 24-hour growth statistics
```sql
CREATE TABLE `statistics_ranking_hour24` (
  `id` int(11) NOT NULL,
  `open_chat_id` int(11) NOT NULL,
  `diff_member` int(11) NOT NULL,
  `percent_increase` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `open_chat_id` (`open_chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**statistics_ranking_week** - Weekly growth statistics
```sql
CREATE TABLE `statistics_ranking_week` (
  `id` int(11) NOT NULL,
  `open_chat_id` int(11) NOT NULL,
  `diff_member` int(11) NOT NULL,
  `percent_increase` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `open_chat_id` (`open_chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

**recommend** - Tag and recommendation data
```sql
CREATE TABLE `recommend` (
  `id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `tag` text NOT NULL,
  `description_comment` text NOT NULL,
  `description_rating` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

#### Important Notes:
- **statistics_ranking_hour tables DO NOT have created_at columns**
- Use `id` for ordering (sequential ranking)
- Join with `open_chat` table for timestamps: `open_chat.created_at`, `open_chat.updated_at`
- For historical data queries, use SQLite statistics table instead

### SQLite Databases (Performance-Optimized)

#### Statistics Database (`/storage/ja/SQLite/statistics/statistics.db`)
```sql
CREATE TABLE IF NOT EXISTS "statistics" (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  open_chat_id INTEGER NOT NULL,
  "member" INTEGER NOT NULL,
  date TEXT NOT NULL
);
CREATE UNIQUE INDEX statistics2_open_chat_id_IDX ON "statistics" (open_chat_id,date);
```

#### Ranking Position Database (`/storage/ja/SQLite/ranking_position/ranking_position.db`)
```sql
CREATE TABLE rising (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  open_chat_id INTEGER NOT NULL,
  category INTEGER NOT NULL,
  "position" INTEGER NOT NULL,
  time TEXT NOT NULL,
  date INTEGER DEFAULT ('2024-01-01') NOT NULL
);

CREATE TABLE ranking (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  open_chat_id INTEGER NOT NULL,
  category INTEGER NOT NULL,
  "position" INTEGER NOT NULL,
  time TEXT NOT NULL,
  date INTEGER DEFAULT ('2024-01-01') NOT NULL
);
```

### Query Examples

**Get current hour growth with chat details:**
```sql
SELECT 
    oc.id,
    oc.name,
    oc.member,
    oc.created_at,
    srh.diff_member,
    srh.percent_increase
FROM statistics_ranking_hour srh
JOIN open_chat oc ON srh.open_chat_id = oc.id
WHERE srh.diff_member > 0
ORDER BY srh.diff_member DESC;
```

**Get historical data from SQLite:**
```sql
SELECT 
    open_chat_id,
    member,
    date
FROM statistics
WHERE date >= date('now', '-7 days')
ORDER BY date ASC;
```

## Testing

### PHPUnit
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test directory
./vendor/bin/phpunit app/Services/test/
./vendor/bin/phpunit app/Models/test/

# Run specific test file
./vendor/bin/phpunit app/Services/Recommend/test/RecommendUpdaterTest.php
```

### Test Structure
- Tests are co-located in `test/` subdirectories
- 50+ test files following `*Test.php` pattern
- Mix of unit and integration tests
- Extends `PHPUnit\Framework\TestCase`

## Code Quality

### Current Tools
- **PHPUnit 9.6**: Testing framework
- **EditorConfig**: Basic formatting rules
- **PSR-4**: Autoloading standard

### Missing Tools
No linting, static analysis, or code formatting tools are currently configured.

## Crawling System

### Parallel Processing
- Crawls ~150,000 OpenChats across 24 categories
- 24 parallel processes for simultaneous downloads
- Custom optimization for high-performance data updates

### Key Files
- `app/Services/OpenChat/OpenChatApiDbMergerWithParallelDownloader.php` - Parent process
- `app/Services/Cron/ParallelDownloadOpenChat.php` - Child process via exec
- `app/Services/OpenChat/OpenChatApiDataParallelDownloader.php` - Data processing

### User Agent
```
Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)
```

## Development Patterns

### Dependency Injection
- Interface-based DI configured in `/shared/MimimalCmsConfig.php`
- Repository pattern with concrete implementations
- SQLite vs MySQL implementations via interface switching

### Autoloading
```php
"psr-4": {
    "Shadow\\": "shadow/",
    "App\\": "app/",
    "Shared\\": "shared/"
}
```

### Configuration
- Environment-specific config in `local-secrets.php` (gitignored)
- Framework config in `/shared/MimimalCMS_*.php` files
- Database credentials in Docker environment variables

## Frontend Components

### Separate Repositories
- Ranking pages: https://github.com/mimimiku778/Open-Chat-Graph-Frontend
- Graph display: https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph  
- Comments: https://github.com/mimimiku778/Open-Chat-Graph-Comments

### Integration
- React components embedded in PHP templates
- Pre-built JavaScript bundles (no build process in main repo)
- Client-side rendering for interactive features

## Deployment

### Scripts Location
- `/batch/sh/` - Deployment and backup scripts
- Database dumps and sync utilities
- Server-specific deployment automation

### Multi-language Support
- Japanese (primary), Thai, Traditional Chinese
- Separate data directories per language
- Internationalized content in `/storage/` subdirectories

## Creating New Pages (MVC Pattern)

### 1. Add Route
In `/app/Config/routing.php`:
```php
Route::path('your-path', [\App\Controllers\Pages\YourController::class, 'method']);
```

### 2. Create Controller
Controllers go in `/app/Controllers/Pages/`:
```php
<?php
declare(strict_types=1);

namespace App\Controllers\Pages;

use Shadow\Kernel\Reception;
use App\Models\Repositories\DB;

class YourController
{
    public function index(Reception $reception)
    {
        // Database access if needed
        DB::connect();
        $stmt = DB::$pdo->prepare("SELECT ...");
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Set meta data
        $_meta = meta();
        $_meta->title = 'Page Title';
        $_meta->description = 'Page description';
        
        // Return view
        return view('view_name', [
            'data' => $data,
            '_meta' => $_meta,
        ]);
    }
}
```

### 3. Create View
Views go in `/app/Views/`:
- Use `.php` extension
- Access variables directly (e.g., `$data`, `$_meta`)
- Use `viewComponent()` for reusable components
- Use `url()` for generating URLs
- Use `t()` for translations
- Use `fileUrl()` for asset URLs

### Important Notes
- Controllers don't use return type hints for view-returning methods
- Use `App\Models\Repositories\DB` (not `Shadow\DB`) for database access
- The DB class automatically selects the correct database based on `MimimalCmsConfig::$urlRoot`
- Meta data is accessed via `meta()` helper function, not a service class
- Views are returned directly with `view()`, not wrapped in Response object