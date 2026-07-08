<?php

declare(strict_types=1);

namespace App\Infrastructure\External;

use App\Domain\Jobs\Job;
use App\Domain\Jobs\JobRepository;
use App\Domain\Jobs\JobSearchCriteria;

class CompositeJobRepository implements JobRepository
{
    /** @var JobRepository */
    private $internalRepository;
    /** @var ExternalJobSource */
    private $externalSource;

    public function __construct(JobRepository $internalRepository, ExternalJobSource $externalSource)
    {
        $this->internalRepository = $internalRepository;
        $this->externalSource = $externalSource;
    }

    public function save(Job $job): void
    {
        $this->internalRepository->save($job);
    }

    public function findById(string $id): ?Job
    {
        return $this->internalRepository->findById($id);
    }

    public function search(JobSearchCriteria $criteria): array
    {
        $internalJobs = $this->internalRepository->search($criteria);

        if ($criteria->source === 'internal') {
            return $internalJobs;
        }

        $externalJobs = $this->externalSource->fetchJobs();
        $filteredExternal = array_filter($externalJobs, fn(Job $job) => $criteria->matches($job));

        $allJobs = array_merge($internalJobs, $filteredExternal);

        usort($allJobs, fn(Job $a, Job $b) => $b->postedAt <=> $a->postedAt);

        $total = count($allJobs);
        $offset = ($criteria->page - 1) * $criteria->perPage;

        return array_slice($allJobs, $offset, $criteria->perPage);
    }

    public function count(JobSearchCriteria $criteria): int
    {
        $internal = $this->internalRepository->count($criteria);

        if ($criteria->source === 'internal') {
            return $internal;
        }

        $external = $this->externalSource->fetchJobs();
        $filtered = array_filter($external, fn(Job $job) => $criteria->matches($job));

        return $internal + count($filtered);
    }
}
