<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Movie\Movie;
use Nette\Database\Table\ActiveRow;

interface RepositoryInterface
{
	/**
	 * @return Entity[]
	 */
	public function findPage(int $limit, int $offset): array;

	public function getById(int $id): ?Entity;

	/**
	 * @param int[] $ids
	 * @return Entity[]
	 */
	public function getByIds(array $ids): array;

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(Entity $movie): array;

	public function fromArray(array $data): Entity;

//    public function mapRow(ActiveRow $row): Entity;
}
