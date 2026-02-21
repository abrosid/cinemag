<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Domain\Movie\MovieSearchService;

require __DIR__ . '/../vendor/autoload.php';

if ($argc < 2) {
	fwrite(STDERR, "Usage: php bin/invalidate-movie-cache.php <movieId>\n");
	exit(1);
}

$movieId = (int) $argv[1];

$container = (new Bootstrap)->bootWebApplication();

/** @var MovieSearchService $service */
$service = $container->getByType(MovieSearchService::class);

$service->invalidateMovieCache($movieId);

fwrite(STDOUT, "Cache invalidated for movie {$movieId}.\n");

