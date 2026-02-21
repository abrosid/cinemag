<?php

declare(strict_types=1);

namespace App\Domain\Movie;

interface CacheClientInterface
{
	/**
	 * @param string[] $keys
	 * @return array<string|null>
	 */
	public function mget(array $keys): array;

	public function setex(string $key, int $ttl, mixed $value): bool;

	/**
	 * @param string[] $keys
	 */
	public function del(array $keys): int;
}
