<?php

declare(strict_types=1);

namespace Domain\Movie;

require __DIR__ . '/../../../vendor/autoload.php';

use App\Domain\Movie\MovieRepository;
use App\Domain\Movie\MovieSearchService;
use Contributte\Elastica\Client as ElasticaClient;
use Elastica\Index;
use Mockery;
use Mockery\MockInterface;
use Predis\ClientInterface;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

Environment::setup();

class MovieSearchServiceTest extends TestCase
{
    /** @var MovieRepository&MockInterface */
    private $repository;
    /** @var ElasticaClient&MockInterface */
    private $elasticaClient;

    /** @var ClientInterface&MockInterface */
    private $redisClient;

	protected function setUp(): void
	{
        $this->elasticaClient = Mockery::mock(ElasticaClient::class);
        $this->redisClient = Mockery::mock(ClientInterface::class);
        $this->repository = Mockery::mock(MovieRepository::class);
	}


    public function testBulkIndexAll(): void
    {
        $this->mockrepozotory();

        // Mock the Elastica client to expect documents being added
        $index = Mockery::mock(Index::class);
        $index->shouldReceive('addDocuments')->with(Mockery::on(function ($documents) {
            return count($documents) === 2 &&
                $documents[0]->getId() === '1' &&
                $documents[1]->getId() === '2';
        }))->once();
        $index->shouldReceive('refresh')->once();
        $this->elasticaClient->shouldReceive('getIndex')->with('movies')->once()->andReturn($index);

        // Create the service with mocked dependencies
        $service = new MovieSearchService($this->elasticaClient, $this->redisClient, $this->repository);

        // Call the method being tested
        $service->bulkIndexAll();

        Assert::true(true);
    }

    private function mockrepozotory(): void
    {
        // Mock the repository to return a list of movies
        $movie1 = (object) ['id' => 1, 'title' => 'Movie 1', 'perex' => 'Action', 'year' => 2020, 'description' => 'Description 1', 'rating' => 8.5, 'price' => 10.0];
        $movie2 = (object) ['id' => 2, 'title' => 'Movie 2', 'perex' => 'Comedy', 'year' => 2021, 'description' => 'Description 2', 'rating' => 7.0, 'price' => 12.0];
        $this->repository->shouldReceive('findPage')->with(100, 0)->andReturn([$movie1, $movie2]);
        $this->repository->shouldReceive('findPage')->with(100, 100)->andReturn([]);
    }

}

(new MovieSearchServiceTest())->run();
