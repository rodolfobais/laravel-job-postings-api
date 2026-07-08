<?php

declare(strict_types=1);

namespace App\Application\Jobs\SearchJobs;

use App\Domain\Jobs\JobRepository;

final class SearchJobsHandler
{
    /** @var JobRepository */
    private $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function handle(SearchJobsQuery $query): array
    {
        $jobs = $this->jobRepository->search($query->criteria);
        $total = $this->jobRepository->count($query->criteria);

        return [
            'data' => $jobs,
            'meta' => [
                'total' => $total,
                'page' => $query->criteria->page,
                'per_page' => $query->criteria->perPage,
                'total_pages' => (int) ceil($total / $query->criteria->perPage),
            ],
        ];
    }
}
