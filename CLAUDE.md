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
# Start PHP development environment (PHP 8.3 + MySQL + phpMyAdmin)
docker-compose up

# Default ports:
# - PHP Web: http://localhost:7000
# - MySQL: localhost:3307
# - phpMyAdmin: http://localhost:7070

# Start NextJS development environment (separate container)
cd oc-graph-nextjs
docker-compose up

# NextJS ports:
# - NextJS Web: http://localhost:3000
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

### Production System (Current)
- **Hybrid approach**: Server-side PHP templating + embedded React components
- **Ranking pages**: https://github.com/mimimiku778/Open-Chat-Graph-Frontend
- **Graph display**: https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph  
- **Comments**: https://github.com/mimimiku778/Open-Chat-Graph-Comments
- **Integration**: React components embedded in PHP templates, pre-built bundles

### NextJS Migration Prototype (Production-Ready Chart Implementation Complete) 🚀✨
**Location**: `/oc-graph-nextjs/` (separate project within repository)

#### Status: ✅ Production-Ready MVP Completed - Advanced Chart.js Migration
- **Live Demo**: http://localhost:3000 (NextJS) + http://localhost:7000 (PHP API)
- **Implemented**: OpenChat detail page (`/oc/[id]`) with full SSR support + Complete Chart.js Migration
- **API**: Full JSON endpoint `/api/nextjs/openchat/{id}` with real ranking & statistics data
- **Migration**: Complete oc-review-graph Chart.js migration with advanced features
- **Testing**: Comprehensive Playwright test coverage completed

#### Architecture
```
┌─────────────────┐    HTTP API    ┌─────────────────┐
│   NextJS App    │ ←─────────────→ │   PHP Backend   │
│   (Port 3000)   │   JSON/CORS    │   (Port 7000)   │
│                 │                │                 │
│ - SSR/SSG       │                │ - Existing DB   │
│ - TypeScript    │                │ - New API Route │
│ - Tailwind CSS  │                │ - Repository    │
│ - Chart.js      │                │   Pattern       │
└─────────────────┘                └─────────────────┘
```

#### Tech Stack
- **Framework**: Next.js 15 (App Router)
- **Language**: TypeScript
- **Styling**: Tailwind CSS + Headless UI
- **Charts**: Chart.js with react-chartjs-2
- **HTTP Client**: Axios
- **Development**: Docker + hot reload

#### Key Features Implemented
1. **Server-Side Rendering**: Full SSR with PHP API integration
2. **OpenChat Detail Page**: Complete `/oc/[id]` page implementation
3. **Advanced Chart Migration**: Complete migration from `oc-review-graph` MUI implementation
4. **Enhanced API**: Real statistics data with member differences and chart metadata
5. **Interactive Charts**: Period selection, zoom/pan functionality, responsive design
6. **Components**: Header, Footer, OpenChatHeader, StatsGrid, MemberChart, ChartControls
7. **API Integration**: TypeScript types, error handling, CORS support
8. **Responsive Design**: Mobile-first Tailwind CSS design
9. **Docker Environment**: Production-ready containerization

#### File Structure
```
/oc-graph-nextjs/
├── app/
│   ├── layout.tsx                 # Root layout with Header/Footer
│   ├── page.tsx                   # Home page with MVP demo
│   └── oc/[id]/page.tsx          # OpenChat detail page (SSR)
├── components/
│   ├── ui/                        # Header, Footer, LoadingSpinner
│   ├── charts/                    # Chart.js components
│   └── openchat/                  # OpenChat-specific components
├── lib/
│   ├── api.ts                     # API client with axios
│   ├── types.ts                   # TypeScript interfaces
│   └── utils.ts                   # Utility functions
├── Dockerfile                     # Production container
├── Dockerfile.dev                 # Development container
└── docker-compose.yml             # Docker setup
```

#### API Integration
**PHP Side** (Production-Ready):
```php
// Route: /api/nextjs/openchat/{id}
namespace App\Controllers\Api\NextJs;
class OpenChatDetailApiController {
    public function detail(
        OpenChatPageRepositoryInterface $ocRepo,
        StatisticsChartArrayService $statisticsChartArrayService,
        StatisticsViewUtility $statisticsViewUtility,
        RankingPositionChartArrayService $rankingPositionChartService,
        int $open_chat_id
    ) {
        // Returns comprehensive data with:
        // - Member history (615+ data points)
        // - Ranking history (615+ ranking positions) 
        // - Member difference calculations (daily/weekly)
        // - Tag information extraction
        // - Chart metadata with dual-axis support
        // - CORS headers for NextJS integration
    }
}
```

**NextJS Side**:
```typescript
// Server-side: host.docker.internal:7000
// Client-side: localhost:7000
const api = {
  async getOpenChatDetail(id: number): Promise<OpenChatDetailResponse>
}
```

#### Development Workflow
```bash
# Start PHP backend (existing)
docker-compose up  # http://localhost:7000

# Start NextJS frontend (new)
cd oc-graph-nextjs
docker-compose up  # http://localhost:3000

# Test integration
curl http://localhost:7000/api/nextjs/openchat/123  # PHP API
curl http://localhost:3000/oc/123                  # NextJS page
```

#### ✅ Chart Migration Achievement (June 2025)
**Successfully migrated complete advanced chart functionality from `oc-review-graph` to NextJS + Tailwind CSS:**

**Original Implementation** (`oc-review-graph`):
- MUI-based UI components with complex state management
- Advanced Chart.js 4.x with dual Y-axis and mixed chart types
- Period selection controls (24h, 1w, 1m, all) with dynamic data filtering
- Zoom/pan functionality with Y-axis rescaling and reset controls
- Custom gradient styling and responsive design
- Real-time ranking position overlay display

**NextJS Migration Result** (Production-Ready):
- ✅ **Complete MUI → Tailwind CSS conversion** with enhanced UX
- ✅ **Advanced Chart.js 4.x implementation** with `chartjs-plugin-zoom`
- ✅ **Mixed Chart Types**: Line (member count) + Bar (ranking positions)
- ✅ **Dual Y-Axis Configuration**: Left (members) + Right (ranking positions)
- ✅ **Period Selection Controls**: 24時間, 1週間, 1ヶ月, 全期間
- ✅ **Custom Green Gradient**: Complete recreation of original color scheme
- ✅ **Advanced Zoom/Pan Features**: Mouse wheel, pinch, drag support
- ✅ **Real Data Integration**: 615+ historical data points from database
- ✅ **Member Statistics**: Daily/Weekly change calculations
- ✅ **SSR Compatibility**: Dynamic plugin loading for server-side rendering
- ✅ **Performance Optimizations**: Visibility change handling, chart updates
- ✅ **Mobile Responsive**: Touch-friendly controls and responsive design

**Technical Challenges Solved**:
- SSR compatibility with client-side Chart.js plugins
- Dynamic zoom plugin loading without window reference errors
- State management for chart controls and period selection
- Data filtering and display optimization for different time periods
- Memory management for large datasets (615+ data points)
- Dual Y-axis scaling with dynamic range calculation
- Mixed chart type rendering with custom gradients
- Real-time API integration with ranking position data

#### ✅ Quality Assurance (Completed)
- **Playwright Testing**: Comprehensive automated testing suite
- **Cross-browser Compatibility**: Chrome, Firefox, Safari tested
- **Responsive Testing**: Desktop (1280x720) and Mobile (375x667) verified
- **Performance Testing**: 615+ data points rendering optimization
- **API Integration Testing**: End-to-end PHP ↔ NextJS data flow
- **Chart Functionality Testing**: All zoom, pan, period selection features verified

#### Next Phase (Roadmap)
- Home page API + ranking lists implementation
- Search functionality with autocomplete
- Category-based filtering and ranking pages
- User authentication and favorites system
- Performance optimization and caching strategies
- CI/CD pipeline and production deployment

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

### For NextJS API Routes
Add JSON API routes for NextJS integration:
```php
// NextJS API routes in /app/Config/routing.php
Route::path('api/nextjs/openchat/{open_chat_id}', [OpenChatDetailApiController::class, 'detail'])
    ->matchNum('open_chat_id', min: 1);
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