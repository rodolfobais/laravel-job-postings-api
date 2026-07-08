<?php

namespace Tests\Unit\Application\Jobs\SearchJobs;

use App\Application\Jobs\SearchJobs\SearchJobsHandler;
use App\Application\Jobs\SearchJobs\SearchJobsQuery;
use App\Domain\Jobs\Job;
use App\Domain\Jobs\JobRepository;
use App\Domain\Jobs\JobSearchCriteria;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SearchJobsHandlerTest extends MockeryTestCase
{
    public function test_returns_data_and_pagination_meta(): void
    {
        $criteria = new JobSearchCriteria(null, null, null, null, null, 'all', 2, 5);
        $jobs = [
            new Job('job-1', 'Backend Developer', new DateTimeImmutable()),
        ];

        $repository = Mockery::mock(JobRepository::class);
        $repository->shouldReceive('search')->once()->with($criteria)->andReturn($jobs);
        $repository->shouldReceive('count')->once()->with($criteria)->andReturn(11);

        $handler = new SearchJobsHandler($repository);
        $result = $handler->handle(new SearchJobsQuery($criteria));

        $this->assertSame($jobs, $result['data']);
        $this->assertSame([
            'total' => 11,
            'page' => 2,
            'per_page' => 5,
            'total_pages' => 3,
        ], $result['meta']);
    }
}
