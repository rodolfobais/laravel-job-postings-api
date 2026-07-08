<?php

declare(strict_types=1);

namespace App\Application\Jobs\CreateJob;

use App\Domain\Jobs\Job;
use App\Domain\Jobs\JobRepository;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final class CreateJobHandler
{
    /** @var JobRepository */
    private $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function handle(CreateJobCommand $command): Job
    {
        $job = new Job(
            Uuid::uuid4()->toString(),
            $command->jobData->title,
            new DateTimeImmutable(),
            $command->jobData->company,
            $command->jobData->location,
            $command->jobData->salaryMin,
            $command->jobData->salaryMax,
            $command->jobData->currency,
            $command->jobData->description,
            $command->jobData->skills
        );

        $this->jobRepository->save($job);

        return $job;
    }
}
