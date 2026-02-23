<?php

declare(strict_types=1);

namespace App\Domain\Movie;

use App\Domain\Entity;
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
    private const PER_PAGE = 100;
    public const LIMIT = 20;

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
		$perPage = self::PER_PAGE;
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
						'perex' => $movie->perex,
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
				'perex' => $movie->perex,
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
	public function searchIds(string $query, int $limit = self::LIMIT): array
	{
		$query = trim($query);
		if ($query == '') {
			return [];
		}

		$multi = (new MultiMatch())
            ->setQuery($query)
            ->setFields(['title^3', 'perex^2', 'description']);
        $bool = (new BoolQuery())
            ->addMust($multi);
		$esQuery = (new Query($bool))->setSize($limit);
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
	 * @return Entity[]
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

			$decoded = Json::decode((string) $item, true);
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
		$perPage = self::PER_PAGE;
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
