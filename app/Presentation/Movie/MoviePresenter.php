<?php

declare(strict_types=1);

namespace App\Presentation\Movie;

use App\Domain\Movie\MovieRepository;
use App\Domain\Movie\MovieSearchService;
use Nette;
use Nette\Application\Responses\JsonResponse;

final class MoviePresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private readonly MovieRepository $movies,
		private readonly MovieSearchService $search,
	) {
		parent::__construct();
	}


	public function renderIndex(int $page = 1, ?string $q = null): void
	{
		$limit = 10;
		$page = max(1, $page);
		$offset = ($page - 1) * $limit;

		if ($q !== null && trim($q) !== '') {
			$ids = $this->search->searchIds($q, $limit * 5);
			$movies = $this->search->loadMoviesFromCache($ids);
			$total = count($movies);
			$movies = array_slice($movies, $offset, $limit);
		} else {
			$movies = $this->movies->findPage($limit, $offset);
			$total = $this->movies->countAll();
		}

		$this->template->movies = $movies;
		$this->template->page = $page;
		$this->template->limit = $limit;
		$this->template->total = $total;
		$this->template->query = $q;
	}


	public function renderDetail(int $id): void
	{
		$movie = $this->movies->getById($id);
		if ($movie === null) {
			$this->error();
		}

		$this->template->movie = $movie;
	}


	public function actionSearch(): void
	{
        $q =  $this->getParameter('q');
		$suggestions = $this->search->searchSuggestions($q, 5);
		$this->sendResponse(new JsonResponse([
			'suggestions' => $suggestions,
		]));
	}
}

