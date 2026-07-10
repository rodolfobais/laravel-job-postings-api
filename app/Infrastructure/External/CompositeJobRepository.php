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
    /** @var ExternalJobSource[] */
    private $externalSources;

    /**
     * @param ExternalJobSource[] $externalSources
     */
    public function __construct(JobRepository $internalRepository, array $externalSources)
    {
        $this->internalRepository = $internalRepository;
        $this->externalSources = $externalSources;
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

        $filteredExternal = array_filter($this->fetchAllExternal(), fn(Job $job) => $criteria->matches($job));

        $allJobs = array_merge($internalJobs, $filteredExternal);

        usort($allJobs, fn(Job $a, Job $b) => $b->postedAt <=> $a->postedAt);

        $offset = ($criteria->page - 1) * $criteria->perPage;

        return array_slice($allJobs, $offset, $criteria->perPage);
    }

    public function count(JobSearchCriteria $criteria): int
    {
        $internal = $this->internalRepository->count($criteria);

        if ($criteria->source === 'internal') {
            return $internal;
        }

        $filtered = array_filter($this->fetchAllExternal(), fn(Job $job) => $criteria->matches($job));

        return $internal + count($filtered);
    }

    /**
     * Fetches every configured external source and merges the results.
     * Adding a new source requires no change here — see AppServiceProvider
     * for where new ExternalJobSource implementations get appended to the
     * array injected into this class.
     *
     * @return Job[]
     */
    private function fetchAllExternal(): array
    {
        $jobs = [];

        foreach ($this->externalSources as $source) {
            $jobs = array_merge($jobs, $source->fetchJobs());
        }

        return $jobs;
    }
}
