<?php

declare(strict_types=1);

namespace App\Domain\Movie;

final class Movie
{
	public function __construct(
		public int $id,
		public string $title,
		public string $parex,
		public int $year,
		public string $description,
		public float $rating,
		public float $price,
		public ?string $thumbnailPath = null,
	)
	{
	}
}

