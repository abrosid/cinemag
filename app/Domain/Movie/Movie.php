<?php

declare(strict_types=1);

namespace App\Domain\Movie;

use App\Domain\Entity;

final class Movie implements Entity
{
	public function __construct(
		public int $id,
		public string $title,
		public string $perex,
		public int $year,
		public string $description,
		public float $rating,
		public float $price,
		public ?string $thumbnailPath = null,
	)
	{
	}
}

