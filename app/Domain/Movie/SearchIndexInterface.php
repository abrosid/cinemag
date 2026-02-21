<?php

declare(strict_types=1);

namespace App\Domain\Movie;

use Elastica\Document;
use Elastica\Query;

interface SearchIndexInterface
{
	public function search(Query $query): object;
	public function addDocument(Document $document): void;
	public function refresh(): void;
	public function addDocuments(array $documents): void;
	public function deleteById(string $id): void;
}
