<?php

declare(strict_types=1);

namespace App\Domain\Movie;

use Elastica\Document;
use Elastica\Exception\ExceptionInterface;
use Elastica\Index;
use Elastica\Query;
use Elastica\ResultSet;

final class ElasticaIndexWrapper implements SearchIndexInterface
{
	public function __construct(
		private readonly Index $index,
	) {
	}

	public function search(Query $query): ResultSet
	{
		return $this->index->search($query);
	}

	public function addDocument(Document $document): void
	{
		$this->index->addDocument($document);
	}

	public function refresh(): void
	{
		$this->index->refresh();
	}

	public function addDocuments(array $documents): void
	{
		$this->index->addDocuments($documents);
	}

	public function deleteById(string $id): void
	{
		$this->index->deleteById($id);
	}
}
