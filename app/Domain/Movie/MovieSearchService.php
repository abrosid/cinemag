<?php

declare(strict_types=1);

namespace App\Domain\Movie;

use Contributte\Elastica\Client as ElasticaClient;
use Elastica\Document;
use Elastica\Exception\ExceptionInterface;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;
use Nette\Utils\Json;
use Predis\ClientInterface;

final class MovieSearchService
{
	private Index $index;


	public function __construct(
		private readonly ElasticaClient $elastica,
		private readonly ClientInterface $redis,
		private readonly MovieRepository $repository,
	) {
		$this->index = $this->elastica->getIndex('movies');
	}


	public function bulkIndexAll(): void
	{
		$perPage = 100;
		$offset = 0;

		while (true) {
			$movies = $this->repository->findPage($perPage, $offset);
			if ($movies === []) {
				break;
			}

			$documents = [];
			foreach ($movies as $movie) {
				$documents[] = new Document(
					(string) $movie->id,
					[
						'title' => $movie->title,
						'parex' => $movie->parex,
						'year' => $movie->year,
						'description' => $movie->description,
						'rating' => $movie->rating,
						'price' => $movie->price,
					],
				);
			}

			if ($documents !== []) {
				$this->index->addDocuments($documents);
				$this->index->refresh();
			}

			$offset += $perPage;
		}
	}


	public function indexMovie(int $movieId): void
	{
		$movie = $this->repository->getById($movieId);
		if ($movie === null) {
			return;
		}

		$document = new Document(
			(string) $movie->id,
			[
				'title' => $movie->title,
				'parex' => $movie->parex,
				'year' => $movie->year,
				'description' => $movie->description,
				'rating' => $movie->rating,
				'price' => $movie->price,
			],
		);

		$this->index->addDocument($document);
		$this->index->refresh();
	}


	public function deleteMovie(int $movieId): void
	{
		try {
			$this->index->deleteById((string) $movieId);
		} catch (ExceptionInterface) {
			// ignore if not present
		}
	}


	/**
	 * @return int[]
	 */
	public function searchIds(string $query, int $limit = 20): array
	{
		$query = trim($query);
		if ($query == '') {
			return [];
		}

		$bool = new BoolQuery;

		$multi = new MultiMatch;
		$multi->setQuery($query);
		$multi->setFields(['title^3', 'parex^2', 'description']);

		$bool->addMust($multi);

		$esQuery = new Query($bool);
		$esQuery->setSize($limit);

		$resultSet = $this->index->search($esQuery);

		$ids = [];
		foreach ($resultSet->getResults() as $result) {
			$ids[] = (int) $result->getId();
		}

		return $ids;
	}


	/**
	 * @return string[]
	 */
	public function searchSuggestions(string $query, int $limit = 5): array
	{
		$ids = $this->searchIds($query, $limit);
		if ($ids === []) {
			return [];
		}

		$movies = $this->loadMoviesFromCache($ids);
		$titles = [];

		foreach ($movies as $movie) {
			$titles[] = $movie->title;
		}

		return $titles;
	}


	/**
	 * @param int[] $ids
	 * @return Movie[]
	 */
	public function loadMoviesFromCache(array $ids): array
	{
		if ($ids === []) {
			return [];
		}

		$moviesById = [];

		$keys = array_map(
			fn (int $id): string => $this->movieKey($id),
			$ids,
		);

		$cached = $this->redis->mget($keys);

		foreach ($ids as $index => $id) {
			$item = $cached[$index] ?? null;
			if ($item === null) {
				continue;
			}

			$decoded = Json::decode((string) $item, Json::FORCE_ARRAY);
			$moviesById[$id] = $this->repository->fromArray($decoded);
		}

		$missingIds = array_values(array_diff($ids, array_keys($moviesById)));
		if ($missingIds !== []) {
			$fallbackMovies = $this->repository->getByIds($missingIds);
			foreach ($fallbackMovies as $movie) {
				$moviesById[$movie->id] = $movie;
				$this->storeMovieInCache($movie);
			}
		}

		$ordered = [];
		foreach ($ids as $id) {
			if (isset($moviesById[$id])) {
				$ordered[] = $moviesById[$id];
			}
		}

		return $ordered;
	}


	public function storeMovieInCache(Movie $movie): void
	{
		$this->redis->setex(
			$this->movieKey($movie->id),
			3600,
			Json::encode($this->repository->toArray($movie)),
		);
	}


	public function invalidateMovieCache(int $movieId): void
	{
		$this->redis->del([$this->movieKey($movieId)]);
	}


	public function warmAllCache(): void
	{
		$perPage = 100;
		$offset = 0;

		while (true) {
			$movies = $this->repository->findPage($perPage, $offset);
			if ($movies === []) {
				break;
			}

			foreach ($movies as $movie) {
				$this->storeMovieInCache($movie);
			}

			$offset += $perPage;
		}
	}


	private function movieKey(int $id): string
	{
		return 'movie:' . $id;
	}
}
