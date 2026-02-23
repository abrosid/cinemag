# Cinemag - Movie Catalog

A Nette-based demo web application for managing and searching a movie catalog.

## Features

- Movie listing and search
- Elasticsearch-powered search functionality
- Redis caching
- MySQL database storage

## Requirements

- Docker & Docker Compose

## Quick Start

1. Clone the repository and navigate to the project directory:
```bash
docker compose up -d --build
```

2. Install PHP dependencies using Composer:
```bash
docker compose run --rm app composer install
```

3. Run database migrations:
```bash
docker compose run --rm app bin/console migrations:continue
```

4. Bulk indexing (init all data in Elasticsearch) :
```bash
docker compose run --rm app php bin/init-elasticsearch.php
```

5. Init all data cache in Redis:
```bash
docker compose run --rm app php bin/init-redis-cache.php
```

6. Access the application at `http://localhost:8080`

## Services

| Service          | Port |
|------------------|------|
| Application      | 8080 |
| MySQL            | 3306 |
| Redis            | 6379 |
| Elasticsearch    | 9200 |

## Tech Stack

- **Framework:** Nette 4.0
- **PHP:** 8.4
- **Dependency Management:** Composer
- **Database:** MySQL 8.0
- **Cache:** Redis 7
- **Search:** Elasticsearch 8.11
- **Templating:** Nette Latte
- **Testing:** Nette Tester
- **Static Analysis:** PHPStan
- **DB migrations:** Nextras\Migrations
- **Console Commands:** Contributte Console (Symfony Console)
- **Containerization:** Docker & Docker Compose

## Commands

```bash
# Run static analysis
composer phpstan

# Run tests
composer tester
```
