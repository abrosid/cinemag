<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Domain\Movie\MovieSearchService;

require __DIR__ . '/../vendor/autoload.php';

$container = (new Bootstrap)->bootWebApplication();

/** @var MovieSearchService $service */
$service = $container->getByType(MovieSearchService::class);

$service->warmAllCache();

fwrite(STDOUT, "Redis cache initialized.\n");

