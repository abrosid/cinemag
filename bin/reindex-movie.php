<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Domain\Movie\MovieSearchService;

require __DIR__ . '/../vendor/autoload.php';

if ($argc < 2) {
	fwrite(STDERR, "Usage: php bin/reindex-movie.php <movieId>\n");
	exit(1);
}

$movieId = (int) $argv[1];

$container = (new Bootstrap)->bootWebApplication();

/** @var MovieSearchService $service */
$service = $container->getByType(MovieSearchService::class);

$service->indexMovie($movieId);

fwrite(STDOUT, "Movie {$movieId} reindexed in Elasticsearch.\n");

