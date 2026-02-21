<?php

declare(strict_types=1);

namespace App\Domain\Movie;

interface MovieRepositoryInterface
{
	/**
	 * @return Movie[]
	 */
	public function findPage(int $limit, int $offset): array;

	public function getById(int $id): ?Movie;

	/**
	 * @param int[] $ids
	 * @return Movie[]
	 */
	public function getByIds(array $ids): array;

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(Movie $movie): array;

	public function fromArray(array $data): Movie;
}
