<?php

declare(strict_types=1);

namespace App\Domain\Movie;

interface CacheInterface
{
	/**
	 * @param string[] $keys
	 * @return array<int, string|null>
	 */
	public function mget(array $keys): array;

	public function setex(string $key, int $ttl, $value): bool;

	/**
	 * @param string[] $keys
	 */
	public function del(array $keys): int;
}
