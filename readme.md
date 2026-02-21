# Cinemag - Movie Catalog

A Nette-based demo web application for managing and searching a movie catalog.

## Features

- Movie listing and search
- Elasticsearch-powered search functionality
- Redis caching
- MySQL database storage

## Requirements

- PHP 8.2+
- Docker & Docker Compose
- Composer

## Quick Start

1. Clone the repository and navigate to the project directory:

```bash
docker compose up -d --build
```
2. Install PHP dependencies using Composer:
```bash
docker compose run --rm app composer install
```

3. Bulk indexing (init all data in Elasticsearch) :

```bash
docker compose run --rm app php bin/init-elasticsearch.php
```

4. Init all data cache in Redis:

```bash
docker compose run --rm app php bin/init-redis-cache.php
```

5. Access the application at `http://localhost:8080`

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
- **Database:** MySQL 8.0
- **Cache:** Redis 7
- **Search:** Elasticsearch 8.11
- **Templating:** Latte
- **Testing:** Tester
- **Static Analysis:** PHPStan

## Commands

```bash
# Run static analysis
composer phpstan

# Run tests
composer tester
```
