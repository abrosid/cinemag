<?php

declare(strict_types=1);

namespace App\Domain\Movie;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

class MovieRepository
{
	public function __construct(
		private readonly Explorer $db,
	) {
	}


	/**
	 * @return Movie[]
	 */
	public function findPage(int $limit, int $offset): array
	{
		$selection = $this->db->table('movie')
			->order('movie_id')
			->limit($limit, $offset);

		$movies = [];
		foreach ($selection as $row) {
			$movies[] = $this->mapRow($row);
		}

		return $movies;
	}


	public function countAll(): int
	{
		return $this->db->table('movie')->count('*');
	}


	public function getById(int $id): ?Movie
	{
		$row = $this->db->table('movie')->get($id);
		return $row ? $this->mapRow($row) : null;
	}


	/**
	 * @param int[] $ids
	 * @return Movie[]
	 */
	public function getByIds(array $ids): array
	{
		if ($ids === []) {
			return [];
		}

		$rows = $this->db->table('movie')
			->where('movie_id', $ids)
			->order('FIELD(movie_id, ?)', $ids);

		$movies = [];
		foreach ($rows as $row) {
			$movies[] = $this->mapRow($row);
		}

		return $movies;
	}


	/**
	 * @return string|null
	 */
	public function findThumbnailPath(int $movieId): ?string
	{
		$row = $this->db->table('movies_files')
			->where('movie_id', $movieId)
			->order('file_id')
			->limit(1)
			->fetch();

		if (!$row) {
			return null;
		}

		$file = $this->db->table('files')->get($row->file_id);
		return $file ? (string) $file->filepath : null;
	}


	public function toArray(Movie $movie): array
	{
		return [
			'id' => $movie->id,
			'title' => $movie->title,
			'parex' => $movie->parex,
			'year' => $movie->year,
			'description' => $movie->description,
			'rating' => $movie->rating,
			'price' => $movie->price,
			'thumbnailPath' => $movie->thumbnailPath,
		];
	}


	public function fromArray(array $data): Movie
	{
		return new Movie(
			id: (int) $data['id'],
			title: (string) $data['title'],
			parex: (string) $data['parex'],
			year: (int) $data['year'],
			description: (string) $data['description'],
			rating: (float) $data['rating'],
			price: (float) $data['price'],
			thumbnailPath: isset($data['thumbnailPath']) ? (string) $data['thumbnailPath'] : null,
		);
	}


	private function mapRow(ActiveRow $row): Movie
	{
		$thumbnailPath = $this->findThumbnailPath((int) $row->movie_id);

		return new Movie(
			id: (int) $row->movie_id,
			title: (string) $row->title,
			parex: (string) $row->parex,
			year: (int) $row->year,
			description: (string) $row->description,
			rating: (float) $row->rating,
			price: (float) $row->price,
			thumbnailPath: $thumbnailPath,
		);
	}
}

