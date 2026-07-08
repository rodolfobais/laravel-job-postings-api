<?php

declare(strict_types=1);

namespace App\Domain\Jobs;

interface JobRepository
{
    public function save(Job $job): void;

    public function findById(string $id): ?Job;

    /**
     * @return Job[]
     */
    public function search(JobSearchCriteria $criteria): array;

    public function count(JobSearchCriteria $criteria): int;
}
